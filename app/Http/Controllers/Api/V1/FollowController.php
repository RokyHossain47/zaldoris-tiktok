<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Stream;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FollowController extends BaseApiController
{
    /**
     * GET /api/v1/users/{id}/followers
     */
    public function getFollowers(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        $query = $request->input('q');
        $followers = User::whereIn('id', function($q) use ($id) {
            $q->select('follower_id')->from('follows')->where('following_id', $id);
        })
        ->when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")->orWhere('username', 'like', "%{$query}%");
        })
        ->paginate($request->input('limit', 20));

        $viewerId = $request->user()?->id;

        $items = collect($followers->items())->map(function($u) use ($viewerId) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'avatarUrl' => $u->avatar_url,
                'role' => $u->role,
                'isVerified' => (bool)$u->is_verified,
                'isFollowing' => $viewerId ? Follow::where('follower_id', $viewerId)->where('following_id', $u->id)->exists() : false,
                'followsMe' => $viewerId ? Follow::where('follower_id', $u->id)->where('following_id', $viewerId)->exists() : false,
            ];
        });

        return $this->paginated($followers, $items, 'Followers retrieved');
    }

    /**
     * GET /api/v1/users/{id}/following
     */
    public function getFollowing(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        $query = $request->input('q');
        $following = User::whereIn('id', function($q) use ($id) {
            $q->select('following_id')->from('follows')->where('follower_id', $id);
        })
        ->when($query, function($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")->orWhere('username', 'like', "%{$query}%");
        })
        ->paginate($request->input('limit', 20));

        $viewerId = $request->user()?->id;

        $items = collect($following->items())->map(function($u) use ($viewerId) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'avatarUrl' => $u->avatar_url,
                'role' => $u->role,
                'isVerified' => (bool)$u->is_verified,
                'isFollowing' => $viewerId ? Follow::where('follower_id', $viewerId)->where('following_id', $u->id)->exists() : false,
            ];
        });

        return $this->paginated($following, $items, 'Following accounts retrieved');
    }

    /**
     * GET /api/v1/me/follow-suggestions
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $user = $request->user();
        $followedIds = Follow::where('follower_id', $user->id)->pluck('following_id')->toArray();
        $followedIds[] = $user->id;

        $suggestions = User::with('creatorProfile')
            ->whereNotIn('id', $followedIds)
            ->whereIn('role', ['creator', 'seller'])
            ->orderBy('is_verified', 'desc')
            ->take(15)
            ->get();

        $data = $suggestions->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'username' => $u->username,
                'avatarUrl' => $u->avatar_url,
                'role' => $u->role,
                'isVerified' => (bool)$u->is_verified,
                'followerCount' => (int)($u->creatorProfile?->follower_count ?: 0),
                'reason' => 'Popular Live Host in your region',
            ];
        });

        return $this->success($data, 'Follow suggestions retrieved');
    }

    /**
     * PUT /api/v1/me/following/{userId}
     */
    public function follow(Request $request, $userId): JsonResponse
    {
        $currentUser = $request->user();
        if ($currentUser->id == $userId) {
            return $this->error('You cannot follow yourself.', 'SELF_FOLLOW_NOT_ALLOWED', 422);
        }

        $targetUser = User::find($userId);
        if (!$targetUser) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        $follow = Follow::firstOrCreate([
            'follower_id' => $currentUser->id,
            'following_id' => $targetUser->id,
        ]);

        if ($targetUser->creatorProfile) {
            $targetUser->creatorProfile->increment('follower_count');
        }

        Notification::create([
            'user_id' => $targetUser->id,
            'title' => 'New Follower! 🎉',
            'message' => $currentUser->name . ' started following you.',
            'type' => 'follow',
            'is_read' => false,
            'action_url' => '/users/' . $currentUser->id,
        ]);

        return $this->success([
            'userId' => $targetUser->id,
            'isFollowing' => true,
            'followerCount' => Follow::where('following_id', $targetUser->id)->count(),
        ], "You are now following {$targetUser->name}");
    }

    /**
     * DELETE /api/v1/me/following/{userId}
     */
    public function unfollow(Request $request, $userId): JsonResponse
    {
        $currentUser = $request->user();
        $targetUser = User::find($userId);
        if (!$targetUser) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        $deleted = Follow::where('follower_id', $currentUser->id)
            ->where('following_id', $targetUser->id)
            ->delete();

        if ($deleted && $targetUser->creatorProfile) {
            $targetUser->creatorProfile->decrement('follower_count');
        }

        return $this->success([
            'userId' => $targetUser->id,
            'isFollowing' => false,
            'followerCount' => Follow::where('following_id', $targetUser->id)->count(),
        ], "Unfollowed {$targetUser->name}");
    }

    /**
     * DELETE /api/v1/me/followers/{userId}
     */
    public function removeFollower(Request $request, $userId): JsonResponse
    {
        $currentUser = $request->user();

        Follow::where('follower_id', $userId)
            ->where('following_id', $currentUser->id)
            ->delete();

        return $this->success(null, 'Follower removed from your list');
    }

    /**
     * POST /api/v1/me/follow-suggestions/{userId}/dismiss
     */
    public function dismissSuggestion(Request $request, $userId): JsonResponse
    {
        return $this->success(['dismissed' => true, 'userId' => (int)$userId], 'Suggestion dismissed');
    }

    /**
     * GET /api/v1/users/{id}/shared-activity
     */
    public function getSharedActivity(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        $recentStreams = Stream::where('user_id', $user->id)->latest()->take(3)->get();
        $recentProducts = Product::where('seller_id', $user->id)->where('status', 'active')->take(4)->get();

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatarUrl' => $user->avatar_url,
            ],
            'streams' => $recentStreams,
            'products' => $recentProducts,
        ], 'Shared creator activity retrieved');
    }
}
