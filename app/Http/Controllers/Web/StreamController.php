<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream;
use App\Models\PkBattle;
use App\Models\Gift;
use App\Models\Product;
use App\Models\StreamMessage;
use App\Services\AgoraService;
use App\Services\BotViewerService;

class StreamController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        $streams = Stream::with(['host', 'products'])
            ->where('is_live', true)
            ->when($type !== 'all', function ($q) use ($type) {
                $q->where('stream_type', $type);
            })
            ->orderBy('is_boosted', 'desc')
            ->orderBy('viewer_count', 'desc')
            ->paginate(12);

        return view('streams.index', compact('streams', 'type'));
    }

    public function show(Request $request, $id = null)
    {
        $stream = $id ? Stream::with(['host.creatorProfile', 'host.sellerProfile', 'products', 'messages.user'])->find($id) : null;
        if (!$stream) {
            $stream = Stream::with(['host.creatorProfile', 'host.sellerProfile', 'products', 'messages.user'])->first() ?? new Stream();
        }

        $gifts = Gift::where('is_active', true)->get();
        $pinnedProduct = ($stream->id && method_exists($stream, 'products')) ? ($stream->products()->wherePivot('is_pinned', true)->first() ?? $stream->products()->first()) : null;

        $agoraService = new AgoraService();
        $user = auth()->user();
        $uid = $user ? $user->id : rand(100000, 999999);
        $agoraToken = $stream->agora_channel ? $agoraService->generateRtcToken($stream->agora_channel, $uid, 'subscriber') : '';

        if ($user && $stream->id) {
            $botService = new BotViewerService();
            $botService->awardWarmUpBonusCoins($user, $stream);
        }

        if ($stream->stream_type === 'pk_battle') {
            $battle = PkBattle::where('stream1_id', $stream->id)
                ->orWhere('stream2_id', $stream->id)
                ->first();
            return view('streams.pk_battle', compact('stream', 'battle', 'gifts', 'agoraToken', 'uid'));
        }

        if ($stream->stream_type === 'live_shopping') {
            return view('streams.live_shopping', compact('stream', 'gifts', 'pinnedProduct', 'agoraToken', 'uid'));
        }

        return view('streams.show', compact('stream', 'gifts', 'pinnedProduct', 'agoraToken', 'uid'));
    }

    public function pkBattle(Request $request, $id = null)
    {
        $battle = $id ? PkBattle::with(['host1', 'host2', 'stream1', 'stream2'])->find($id) : null;
        if (!$battle) {
            $battle = PkBattle::with(['host1', 'host2', 'stream1', 'stream2'])->first();
        }

        $stream = $battle ? $battle->stream1 : (Stream::first() ?? new Stream());
        $gifts = Gift::where('is_active', true)->get();

        $agoraService = new AgoraService();
        $uid = auth()->check() ? auth()->id() : rand(100000, 999999);
        $agoraToken = ($stream && $stream->agora_channel) ? $agoraService->generateRtcToken($stream->agora_channel, $uid, 'subscriber') : '';

        return view('streams.pk_battle', compact('battle', 'stream', 'gifts', 'agoraToken', 'uid'));
    }
}
