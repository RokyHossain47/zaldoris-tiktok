<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Stream;
use App\Models\StreamMessage;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LiveStreamController extends BaseApiController
{
    /**
     * GET /api/v1/live-sessions
     */
    public function index(Request $request): JsonResponse
    {
        $type = $request->input('type'); // 'shopping', 'auction', 'academy', 'gaming', 'pk_battle'
        $status = $request->input('status', 'live');

        $query = Stream::with('host');

        if ($status !== 'all') {
            $query->where('is_live', $status === 'live');
        }

        if ($type) {
            $query->where('type', $type);
        }

        $streams = $query->orderBy('viewer_count', 'desc')->paginate($request->input('limit', 20));

        $items = collect($streams->items())->map(function($s) {
            return [
                'id' => $s->id,
                'title' => $s->title,
                'type' => $s->type ?: 'shopping',
                'isLive' => (bool)$s->is_live,
                'viewerCount' => (int)$s->viewer_count,
                'likeCount' => (int)$s->like_count,
                'shareCount' => (int)($s->share_count ?: 0),
                'thumbnailUrl' => $s->thumbnail_url ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800',
                'hlsPlaybackUrl' => $s->hls_playback_url ?: 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'host' => [
                    'id' => $s->host?->id,
                    'name' => $s->host?->name ?: 'Live Host',
                    'username' => $s->host?->username,
                    'avatarUrl' => $s->host?->avatar_url,
                    'isVerified' => (bool)$s->host?->is_verified,
                ],
                'startedAt' => $s->started_at?->toISOString(),
            ];
        });

        return $this->paginated($streams, $items, 'Live streaming directories retrieved');
    }

    /**
     * GET /api/v1/live-sessions/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $stream = Stream::with(['host.creatorProfile', 'products'])->find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'STREAM_NOT_FOUND', 404);
        }

        $pinnedProduct = $stream->products()->wherePivot('is_pinned', true)->first() ?: $stream->products()->first();

        return $this->success([
            'id' => $stream->id,
            'title' => $stream->title,
            'description' => $stream->description,
            'type' => $stream->type ?: 'shopping',
            'isLive' => (bool)$stream->is_live,
            'viewerCount' => (int)$stream->viewer_count,
            'likeCount' => (int)$stream->like_count,
            'shareCount' => (int)($stream->share_count ?: 0),
            'playback' => [
                'hlsUrl' => $stream->hls_playback_url ?: 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                'rtmpUrl' => "rtmp://live.zaldoris.com/live/{$stream->stream_key}",
                'streamKey' => $stream->stream_key,
                'latency' => 'ultra_low',
            ],
            'host' => [
                'id' => $stream->host?->id,
                'name' => $stream->host?->name ?: 'Live Host',
                'username' => $stream->host?->username,
                'avatarUrl' => $stream->host?->avatar_url,
                'followersCount' => (int)($stream->host?->creatorProfile?->follower_count ?: 0),
                'isVerified' => (bool)$stream->host?->is_verified,
            ],
            'pinnedItem' => $pinnedProduct ? [
                'id' => $pinnedProduct->id,
                'type' => 'product',
                'title' => $pinnedProduct->title,
                'price' => (float)$pinnedProduct->price,
                'priceMinor' => $this->toMinorUnits($pinnedProduct->price),
                'currency' => setting('currency_code', 'CAD'),
                'image' => $pinnedProduct->primary_image,
                'inStock' => $pinnedProduct->available_stock > 0,
            ] : null,
            'allowedActions' => ['chat', 'gift', 'react', 'purchase', 'share'],
        ], 'Live session details and overlay state retrieved');
    }

    /**
     * POST /api/v1/live-sessions/{id}/join
     */
    public function join(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        $stream->increment('viewer_count');

        return $this->success([
            'sessionId' => $stream->id,
            'playbackToken' => 'pb_' . Str::random(32),
            'chatChannel' => "live_stream_{$stream->id}",
            'heartbeatIntervalSeconds' => 30,
            'viewerCount' => (int)$stream->viewer_count,
        ], 'Joined live session. Realtime credentials granted.');
    }

    /**
     * POST /api/v1/live-sessions/{id}/heartbeat
     */
    public function heartbeat(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        return $this->success([
            'acknowledged' => true,
            'viewerCount' => $stream ? (int)$stream->viewer_count : 0,
            'isLive' => $stream ? (bool)$stream->is_live : false,
        ], 'Viewer presence maintained');
    }

    /**
     * POST /api/v1/live-sessions/{id}/leave
     */
    public function leave(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        if ($stream && $stream->viewer_count > 0) {
            $stream->decrement('viewer_count');
        }

        return $this->success(null, 'Viewer presence ended');
    }

    /**
     * GET /api/v1/live-sessions/{id}/comments
     */
    public function getComments(Request $request, $id): JsonResponse
    {
        $messages = StreamMessage::with('user')
            ->where('stream_id', $id)
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        $items = $messages->map(function($m) {
            return [
                'id' => $m->id,
                'message' => $m->message,
                'isPinned' => (bool)$m->is_pinned,
                'isHighlighted' => (bool)$m->is_highlighted,
                'createdAt' => $m->created_at->toISOString(),
                'user' => [
                    'id' => $m->user?->id,
                    'name' => $m->user?->name ?: 'Viewer',
                    'avatarUrl' => $m->user?->avatar_url,
                    'isVip' => (bool)$m->user?->is_vip,
                ]
            ];
        });

        return $this->success($items, 'Live stream comments retrieved');
    }

    /**
     * POST /api/v1/live-sessions/{id}/comments
     */
    public function sendComment(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:300',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        $msg = StreamMessage::create([
            'stream_id' => $stream->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => 'chat',
        ]);

        return $this->success([
            'id' => $msg->id,
            'message' => $msg->message,
            'createdAt' => $msg->created_at->toISOString(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatarUrl' => $user->avatar_url,
            ]
        ], 'Comment dispatched', [], 201);
    }

    /**
     * POST /api/v1/live-sessions/{id}/reactions
     */
    public function sendReaction(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        $count = (int) $request->input('count', 1);
        $stream->increment('like_count', $count);

        return $this->success([
            'reaction' => $request->input('reaction_type', 'heart'),
            'addedCount' => $count,
            'totalLikes' => (int)$stream->like_count,
        ], 'Reaction broadcasted');
    }

    /**
     * POST /api/v1/live-sessions/{id}/shares
     */
    public function recordShare(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        $stream->increment('share_count');
        $shareUrl = url("/live/{$stream->id}");

        return $this->success([
            'shareUrl' => $shareUrl,
            'totalShares' => (int)$stream->share_count,
        ], 'Share link created');
    }

    /**
     * GET /api/v1/live-sessions/{id}/items
     */
    public function getItems(Request $request, $id): JsonResponse
    {
        $stream = Stream::with('products')->find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        $items = $stream->products->map(function($p) {
            return [
                'id' => $p->id,
                'type' => 'product',
                'title' => $p->title,
                'price' => (float)$p->price,
                'priceMinor' => $this->toMinorUnits($p->price),
                'currency' => setting('currency_code', 'CAD'),
                'image' => $p->primary_image,
                'isPinned' => (bool)$p->pivot->is_pinned,
                'inStock' => $p->available_stock > 0,
            ];
        });

        return $this->success($items, 'Stream catalog items retrieved');
    }

    /**
     * PUT /api/v1/live-sessions/{id}/pinned-item
     */
    public function pinItem(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        // Host verification
        if ($stream->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return $this->error('Only the host can pin items.', 'FORBIDDEN', 403);
        }

        // Unpin all previous items
        $stream->products()->updateExistingPivot($stream->products->pluck('id'), ['is_pinned' => false]);

        // Attach or update pin
        if ($stream->products()->where('product_id', $request->product_id)->exists()) {
            $stream->products()->updateExistingPivot($request->product_id, ['is_pinned' => true]);
        } else {
            $stream->products()->attach($request->product_id, ['is_pinned' => true]);
        }

        $product = Product::find($request->product_id);

        return $this->success([
            'pinned' => true,
            'product' => [
                'id' => $product->id,
                'title' => $product->title,
                'price' => (float)$product->price,
                'image' => $product->primary_image,
            ]
        ], 'Product pinned to live overlay');
    }

    /**
     * DELETE /api/v1/live-sessions/{id}/pinned-item
     */
    public function unpinItem(Request $request, $id): JsonResponse
    {
        $stream = Stream::find($id);
        if (!$stream) {
            return $this->error('Live session not found', 'NOT_FOUND', 404);
        }

        if ($stream->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return $this->error('Only the host can remove pinned items.', 'FORBIDDEN', 403);
        }

        $stream->products()->updateExistingPivot($stream->products->pluck('id'), ['is_pinned' => false]);

        return $this->success(null, 'Pinned overlay item removed');
    }
}
