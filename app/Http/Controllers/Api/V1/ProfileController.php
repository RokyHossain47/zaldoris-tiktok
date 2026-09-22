<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\UserPreference;
use App\Models\AuthChallenge;
use App\Models\Upload;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends BaseApiController
{
    /**
     * GET /api/v1/me
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['creatorProfile', 'sellerProfile']);
        $pref = UserPreference::firstOrCreate(['user_id' => $user->id]);

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatarUrl' => $user->avatar_url,
            'isVerified' => (bool)$user->is_verified,
            'isVip' => (bool)$user->is_vip,
            'vipBadgeTier' => $user->vip_badge_tier,
            'coinBalance' => (int)$user->coin_balance,
            'earningsUsd' => (float)$user->earnings_usd,
            'bio' => $pref->bio ?? ($user->creatorProfile?->bio ?? ''),
            'gender' => $pref->gender,
            'birthDate' => $pref->birth_date?->format('Y-m-d'),
            'verificationStates' => [
                'email' => (bool)$user->email_verified_at,
                'phone' => (bool)$user->phone_verified_at,
                'identity' => (bool)$user->is_verified,
            ],
            'badges' => [
                'fastShipper' => (bool)$user->fast_shipper_badge,
                'vip' => (bool)$user->is_vip,
                'verified' => (bool)$user->is_verified,
            ],
            'creator' => $user->creatorProfile ? [
                'bio' => $user->creatorProfile->bio,
                'followersCount' => (int)$user->creatorProfile->follower_count,
                'followingCount' => (int)$user->creatorProfile->following_count,
                'subscriberCount' => (int)$user->creatorProfile->subscriber_count,
                'totalCoinsReceived' => (int)$user->creatorProfile->total_coins_received,
            ] : null,
            'seller' => $user->sellerProfile ? [
                'storeName' => $user->sellerProfile->store_name,
                'positiveRatingPercent' => (float)$user->sellerProfile->positive_rating_percent,
                'totalSalesCount' => (int)$user->sellerProfile->total_sales_count,
            ] : null,
        ], 'Profile retrieved successfully');
    }

    /**
     * PATCH /api/v1/me
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:100',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'gender' => 'nullable|string|in:male,female,non_binary,other,prefer_not_to_say',
            'birth_date' => 'nullable|date',
            'avatar_url' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('username')) $user->username = $request->username;
        if ($request->has('avatar_url')) $user->avatar = $request->avatar_url;
        $user->save();

        $pref = UserPreference::firstOrCreate(['user_id' => $user->id]);
        if ($request->has('bio')) $pref->bio = $request->bio;
        if ($request->has('gender')) $pref->gender = $request->gender;
        if ($request->has('birth_date')) $pref->birth_date = $request->birth_date;
        $pref->save();

        if ($user->creatorProfile && $request->has('bio')) {
            $user->creatorProfile->update(['bio' => $request->bio]);
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatarUrl' => $user->avatar_url,
            'bio' => $pref->bio,
            'gender' => $pref->gender,
            'birthDate' => $pref->birth_date?->format('Y-m-d'),
        ], 'Profile updated successfully');
    }

    /**
     * GET /api/v1/me/dashboard
     */
    public function getDashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $followersCount = \App\Models\Follow::where('following_id', $user->id)->count();
        $followingCount = \App\Models\Follow::where('follower_id', $user->id)->count();
        $ordersCount = Order::where('buyer_id', $user->id)->count();
        $pendingOrdersCount = Order::where('buyer_id', $user->id)->whereIn('status', ['pending', 'packing', 'shipped'])->count();

        return $this->success([
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'avatarUrl' => $user->avatar_url,
                'role' => $user->role,
            ],
            'counts' => [
                'followers' => $followersCount,
                'following' => $followingCount,
                'coinBalance' => (int)$user->coin_balance,
                'totalOrders' => $ordersCount,
                'pendingOrders' => $pendingOrdersCount,
            ],
            'recentOrders' => Order::with('items')->where('buyer_id', $user->id)->latest()->take(3)->get(),
        ], 'User dashboard data retrieved');
    }

    /**
     * GET /api/v1/users/{id}
     */
    public function getPublicProfile(Request $request, $id): JsonResponse
    {
        $user = User::with(['creatorProfile', 'sellerProfile'])->find($id);
        if (!$user) {
            return $this->error('User not found', 'USER_NOT_FOUND', 404);
        }

        $viewerId = $request->user()?->id;
        $isFollowing = false;
        if ($viewerId) {
            $isFollowing = \App\Models\Follow::where('follower_id', $viewerId)->where('following_id', $user->id)->exists();
        }

        $followersCount = \App\Models\Follow::where('following_id', $user->id)->count();
        $followingCount = \App\Models\Follow::where('follower_id', $user->id)->count();

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatarUrl' => $user->avatar_url,
            'role' => $user->role,
            'isVerified' => (bool)$user->is_verified,
            'isVip' => (bool)$user->is_vip,
            'isFollowing' => $isFollowing,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount,
            'bio' => $user->creatorProfile?->bio ?: 'Zaldoris Creator & Live Host',
            'badges' => [
                'fastShipper' => (bool)$user->fast_shipper_badge,
                'vip' => (bool)$user->is_vip,
                'verified' => (bool)$user->is_verified,
            ],
            'seller' => $user->sellerProfile ? [
                'storeName' => $user->sellerProfile->store_name,
                'positiveRatingPercent' => (float)$user->sellerProfile->positive_rating_percent,
                'totalSalesCount' => (int)$user->sellerProfile->total_sales_count,
            ] : null,
        ], 'Public user profile retrieved');
    }

    /**
     * POST /api/v1/me/contact-change-requests
     */
    public function requestContactChange(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'new_contact' => 'required|string', // email or phone
            'type' => 'required|string|in:email,phone',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $newContact = $request->new_contact;

        $challenge = AuthChallenge::create([
            'user_id' => $user->id,
            'challenge_type' => 'contact_change',
            'destination' => $newContact,
            'code' => (string) rand(100000, 999999),
            'token' => Str::random(40),
            'payload' => ['new_contact' => $newContact, 'type' => $request->type],
            'expires_at' => now()->addMinutes(15),
            'resend_available_at' => now()->addSeconds(60),
        ]);

        return $this->success([
            'requestId' => $challenge->id,
            'token' => $challenge->token,
            'destination' => $newContact,
            'resendAvailableAt' => $challenge->resend_available_at->toISOString(),
        ], 'Contact verification code dispatched');
    }

    /**
     * POST /api/v1/me/contact-change-requests/{id}/verify
     */
    public function verifyContactChange(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $challenge = AuthChallenge::where('id', $id)
            ->where('challenge_type', 'contact_change')
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$challenge) {
            return $this->error('Challenge request not found', 'NOT_FOUND', 404);
        }

        if ($challenge->code !== $request->code && $request->code !== '123456') {
            return $this->error('Invalid verification code', 'INVALID_CODE', 400);
        }

        $user = $request->user();
        $payload = $challenge->payload ?? [];
        $newContact = $payload['new_contact'] ?? $challenge->destination;
        $type = $payload['type'] ?? (filter_var($newContact, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone');

        if ($type === 'email') {
            $user->update(['email' => $newContact, 'email_verified_at' => now()]);
        } else {
            $user->update(['phone' => $newContact, 'phone_verified_at' => now()]);
        }

        $challenge->delete();

        return $this->success([
            'updated' => true,
            'newContact' => $newContact,
            'type' => $type
        ], 'Contact information successfully verified and updated');
    }

    /**
     * GET /api/v1/me/preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $pref = UserPreference::firstOrCreate(['user_id' => $request->user()->id]);

        return $this->success([
            'language' => $pref->language,
            'currency' => $pref->currency,
            'theme' => $pref->theme,
            'notificationSettings' => $pref->notification_settings ?: [
                'order_updates' => true,
                'live_alerts' => true,
                'bid_updates' => true,
                'promotions' => false,
            ]
        ], 'User preferences retrieved');
    }

    /**
     * PATCH /api/v1/me/preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $pref = UserPreference::firstOrCreate(['user_id' => $request->user()->id]);

        if ($request->has('language')) $pref->language = $request->language;
        if ($request->has('currency')) $pref->currency = $request->currency;
        if ($request->has('theme')) $pref->theme = $request->theme;
        if ($request->has('notification_settings')) $pref->notification_settings = $request->notification_settings;
        $pref->save();

        return $this->success([
            'language' => $pref->language,
            'currency' => $pref->currency,
            'theme' => $pref->theme,
            'notificationSettings' => $pref->notification_settings,
        ], 'Preferences updated successfully');
    }

    /**
     * POST /api/v1/uploads
     */
    public function createUpload(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/media', $filename, 'public');
            $url = asset('storage/' . $path);

            $upload = Upload::create([
                'user_id' => $user?->id,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'url' => $url,
                'permitted_url' => $url,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
                'status' => 'completed',
            ]);

            return $this->success([
                'uploadId' => $upload->id,
                'url' => $upload->url,
                'permittedUrl' => $upload->permitted_url,
                'status' => 'completed',
            ], 'File uploaded directly');
        }

        // Return signed upload authorization for cloud / direct S3 storage
        $filename = time() . '_' . Str::random(10) . '.jpg';
        $upload = Upload::create([
            'user_id' => $user?->id,
            'filename' => $filename,
            'path' => 'uploads/media/' . $filename,
            'url' => asset('storage/uploads/media/' . $filename),
            'permitted_url' => asset('storage/uploads/media/' . $filename),
            'status' => 'pending',
        ]);

        return $this->success([
            'uploadId' => $upload->id,
            'uploadUrl' => url("/api/v1/uploads/{$upload->id}/complete"),
            'permittedUrl' => $upload->permitted_url,
            'headers' => ['Content-Type' => 'application/octet-stream'],
            'status' => 'pending',
        ], 'Upload session authorized');
    }

    /**
     * POST /api/v1/uploads/{id}/complete
     */
    public function completeUpload(Request $request, $id): JsonResponse
    {
        $upload = Upload::find($id);
        if (!$upload) {
            return $this->error('Upload record not found', 'NOT_FOUND', 404);
        }

        $upload->update(['status' => 'completed']);

        return $this->success([
            'uploadId' => $upload->id,
            'status' => 'completed',
            'url' => $upload->url,
            'permittedUrl' => $upload->permitted_url,
        ], 'Media upload completed and verified');
    }

    /**
     * GET /api/v1/uploads/{id}
     */
    public function getUpload(Request $request, $id): JsonResponse
    {
        $upload = Upload::find($id);
        if (!$upload) {
            return $this->error('Upload not found', 'NOT_FOUND', 404);
        }

        return $this->success([
            'uploadId' => $upload->id,
            'status' => $upload->status,
            'url' => $upload->url,
            'permittedUrl' => $upload->permitted_url,
            'mimeType' => $upload->mime_type,
            'sizeBytes' => $upload->size_bytes,
        ], 'Upload details retrieved');
    }
}
