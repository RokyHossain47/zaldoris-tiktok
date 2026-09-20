<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Notification;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class NotificationController extends BaseApiController
{
    /**
     * GET /api/v1/me/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->paginate($request->input('limit', 20));

        $items = collect($notifications->items())->map(function($n) {
            return [
                'id' => $n->id,
                'type' => $n->type ?: 'general', // 'auction_won', 'outbid', 'live_started', 'order_shipped', 'gift_received', 'payment_update'
                'title' => $n->title,
                'body' => $n->message,
                'isRead' => (bool)$n->is_read,
                'actionUrl' => $n->action_url,
                'createdAt' => $n->created_at->toISOString(),
            ];
        });

        return $this->paginated($notifications, $items, 'Notifications retrieved');
    }

    /**
     * GET /api/v1/me/notifications/unread-count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return $this->success(['unreadCount' => $count], 'Unread notification count retrieved');
    }

    /**
     * PATCH /api/v1/me/notifications/{id}
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return $this->success(['id' => (int)$id, 'isRead' => true], 'Notification marked as read');
    }

    /**
     * POST /api/v1/me/notifications/read-all
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->success(null, 'All notifications marked as read');
    }

    /**
     * PUT /api/v1/me/devices/{installationId}
     */
    public function registerDevice(Request $request, string $installationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'push_token' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android,web',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $device = UserDevice::updateOrCreate(
            ['installation_id' => $installationId],
            [
                'user_id' => $request->user()->id,
                'push_token' => $request->push_token,
                'device_type' => $request->input('device_type', 'android'),
                'os_version' => $request->input('os_version', 'Android 14'),
                'app_version' => $request->input('app_version', '1.0.0'),
            ]
        );

        return $this->success([
            'installationId' => $device->installation_id,
            'deviceType' => $device->device_type,
            'registered' => true,
        ], 'Push device token registered successfully');
    }

    /**
     * DELETE /api/v1/me/devices/{installationId}
     */
    public function unregisterDevice(Request $request, string $installationId): JsonResponse
    {
        UserDevice::where('installation_id', $installationId)
            ->where('user_id', $request->user()->id)
            ->delete();

        return $this->success(null, 'Device token unregistered');
    }
}
