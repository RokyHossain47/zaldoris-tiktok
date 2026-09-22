<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Report;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ModerationController extends BaseApiController
{
    /**
     * POST /api/v1/reports
     */
    public function submitReport(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'target_type' => 'required|string|in:user,stream,comment,message,product,auction',
            'target_id' => 'required|integer',
            'reason' => 'required|string|max:150',
            'details' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'reason' => $request->reason,
            'details' => $request->details ?: $request->description,
            'status' => 'pending',
        ]);

        return $this->success([
            'reportId' => $report->id,
            'status' => 'received',
            'message' => 'Thank you for helping keep our community safe. Our trust & safety team will review this report.',
        ], 'Report submitted for review', [], 201);
    }

    /**
     * PUT /api/v1/me/blocked-users/{userId}
     */
    public function blockUser(Request $request, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;
        if ($currentUserId == $userId) {
            return $this->error('You cannot block yourself.', 'SELF_BLOCK_ERROR', 422);
        }

        $target = User::find($userId);
        if (!$target) {
            return $this->error('User not found', 'NOT_FOUND', 404);
        }

        BlockedUser::firstOrCreate([
            'user_id' => $currentUserId,
            'blocked_user_id' => $target->id,
        ]);

        // Unfollow mutually
        \App\Models\Follow::where(function($q) use ($currentUserId, $target) {
            $q->where('follower_id', $currentUserId)->where('following_id', $target->id);
        })->orWhere(function($q) use ($currentUserId, $target) {
            $q->where('follower_id', $target->id)->where('following_id', $currentUserId);
        })->delete();

        return $this->success([
            'blockedUserId' => $target->id,
            'isBlocked' => true,
        ], "User {$target->name} has been blocked.");
    }

    /**
     * DELETE /api/v1/me/blocked-users/{userId}
     */
    public function unblockUser(Request $request, $userId): JsonResponse
    {
        $currentUserId = $request->user()->id;

        BlockedUser::where('user_id', $currentUserId)
            ->where('blocked_user_id', $userId)
            ->delete();

        return $this->success(null, 'User has been unblocked.');
    }
}
