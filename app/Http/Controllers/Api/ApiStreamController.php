<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream;
use App\Models\StreamMessage;
use App\Services\AgoraService;
use App\Services\AiModerationService;
use App\Services\BotViewerService;

class ApiStreamController extends Controller
{
    /**
     * List active live streams for Mobile Discovery Feed.
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // live_shopping, live_auction, pk_battle, standard
        $category = $request->query('category');

        $query = Stream::with(['host', 'products'])
            ->where('is_live', true);

        if ($type && $type !== 'all') {
            $query->where('stream_type', $type);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        // New seller discovery boost (SRS #11)
        $streams = $query->orderBy('is_boosted', 'desc')
            ->orderBy('viewer_count', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $streams,
        ]);
    }

    /**
     * Create / Start a New Live Stream.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'stream_type' => 'required|in:standard,live_shopping,live_auction,pk_battle',
            'thumbnail_url' => 'nullable|string',
        ]);

        $channelName = 'stream_' . $user->id . '_' . time();
        $agoraService = new AgoraService();
        $token = $agoraService->generateRtcToken($channelName, $user->id, 'publisher');

        // Check if seller's first 5 streams for discovery boost (SRS #11)
        $sellerStreamsCount = Stream::where('host_id', $user->id)->count();
        $isBoosted = $sellerStreamsCount < 5;

        $stream = Stream::create([
            'host_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'stream_type' => $validated['stream_type'],
            'agora_channel' => $channelName,
            'agora_token' => $token,
            'thumbnail_url' => $validated['thumbnail_url'] ?? 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
            'is_live' => true,
            'viewer_count' => 1,
            'is_boosted' => $isBoosted,
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Live stream created successfully.',
            'stream' => $stream->load('host'),
            'agora_app_id' => $agoraService->getAppId(),
            'agora_token' => $token,
            'agora_channel' => $channelName,
        ], 201);
    }

    /**
     * Get Stream Details & Join Room.
     */
    public function show(Request $request, $id)
    {
        $stream = Stream::with(['host.creatorProfile', 'host.sellerProfile', 'products', 'auctions.bids.user'])
            ->findOrFail($id);

        $agoraService = new AgoraService();
        $user = $request->user();
        $uid = $user ? $user->id : rand(100000, 999999);
        $subscriberToken = $agoraService->generateRtcToken($stream->agora_channel, $uid, 'subscriber');

        // Increment viewer count
        $stream->increment('viewer_count');

        // Check SRS #11 bonus coins for joining
        $bonusInfo = ['awarded' => false, 'bonus_coins' => 0];
        if ($user) {
            $botService = new BotViewerService();
            $bonusInfo = $botService->awardWarmUpBonusCoins($user, $stream);
        }

        return response()->json([
            'success' => true,
            'stream' => $stream,
            'agora_app_id' => $agoraService->getAppId(),
            'agora_token' => $subscriberToken,
            'uid' => $uid,
            'bonus_coins_reward' => $bonusInfo,
        ]);
    }

    /**
     * Send Live Chat Message (with AI Moderation).
     */
    public function sendMessage(Request $request, $id)
    {
        $stream = Stream::findOrFail($id);
        $user = $request->user();

        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        // AI Moderation check (SRS Page 5)
        $aiService = new AiModerationService();
        $modCheck = $aiService->moderateText($request->message, 'stream_chat', $user ? $user->id : null, $stream->id);

        if (!$modCheck['approved']) {
            return response()->json([
                'success' => false,
                'message' => $modCheck['reason'],
            ], 422);
        }

        $msg = StreamMessage::create([
            'stream_id' => $stream->id,
            'user_id' => $user ? $user->id : null,
            'username_display' => $user ? $user->name : 'Guest_' . rand(100, 999),
            'message' => $request->message,
            'is_bot' => false,
            'message_type' => 'chat',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent.',
            'chat_message' => $msg->load('user'),
        ]);
    }

    /**
     * Get Stream Chat Messages.
     */
    public function getMessages($id)
    {
        $messages = StreamMessage::with('user')
            ->where('stream_id', $id)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Warm-up Bot Message Trigger (SRS #11).
     */
    public function triggerBotWarmUp($id)
    {
        $stream = Stream::findOrFail($id);
        $botService = new BotViewerService();
        $result = $botService->warmUpStream($stream);

        return response()->json([
            'success' => true,
            'warmup' => $result,
        ]);
    }

    /**
     * End Live Stream.
     */
    public function endStream(Request $request, $id)
    {
        $stream = Stream::findOrFail($id);
        if ($stream->host_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $stream->is_live = false;
        $stream->ended_at = now();
        $stream->save();

        return response()->json([
            'success' => true,
            'message' => 'Stream ended successfully.',
            'stats' => [
                'total_sales' => $stream->total_sales_amount,
                'total_gifts' => $stream->total_gift_coins,
                'peak_viewers' => $stream->viewer_count,
            ],
        ]);
    }
}
