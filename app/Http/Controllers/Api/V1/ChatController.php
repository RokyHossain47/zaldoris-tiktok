<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChatController extends BaseApiController
{
    /**
     * GET /api/v1/me/conversations
     */
    public function getConversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with(['userOne', 'userTwo'])
            ->where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->orderBy('last_message_at', 'desc')
            ->paginate($request->input('limit', 20));

        $items = collect($conversations->items())->map(function($c) use ($userId) {
            $partner = ($c->user_one_id === $userId) ? $c->userTwo : $c->userOne;
            $unreadCount = ChatMessage::where('conversation_id', $c->id)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();

            return [
                'conversationId' => $c->id,
                'partner' => [
                    'id' => $partner?->id,
                    'name' => $partner?->name ?: 'User',
                    'username' => $partner?->username,
                    'avatarUrl' => $partner?->avatar_url,
                    'isVerified' => (bool)$partner?->is_verified,
                ],
                'lastMessage' => $c->last_message,
                'lastMessageAt' => $c->last_message_at?->toISOString(),
                'unreadCount' => $unreadCount,
            ];
        });

        return $this->paginated($conversations, $items, 'Direct conversations retrieved');
    }

    /**
     * POST /api/v1/conversations
     */
    public function createOrGetConversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;
        $recipientId = (int) $request->recipient_id;

        if ($userId === $recipientId) {
            return $this->error('Cannot start a direct conversation with yourself', 'INVALID_RECIPIENT', 422);
        }

        $minId = min($userId, $recipientId);
        $maxId = max($userId, $recipientId);

        $conversation = Conversation::firstOrCreate(
            ['user_one_id' => $minId, 'user_two_id' => $maxId]
        );

        $partner = User::find($recipientId);

        return $this->success([
            'conversationId' => $conversation->id,
            'partner' => [
                'id' => $partner?->id,
                'name' => $partner?->name,
                'avatarUrl' => $partner?->avatar_url,
            ],
            'createdAt' => $conversation->created_at->toISOString(),
        ], 'Direct conversation ready', [], 201);
    }

    /**
     * GET /api/v1/conversations/{id}/messages
     */
    public function getMessages(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        $conversation = Conversation::find($id);

        if (!$conversation || ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId)) {
            return $this->error('Conversation not found or access denied', 'FORBIDDEN', 403);
        }

        $messages = ChatMessage::with('sender')
            ->where('conversation_id', $id)
            ->latest()
            ->paginate($request->input('limit', 30));

        $items = collect($messages->items())->reverse()->values()->map(function($m) use ($userId) {
            return [
                'id' => $m->id,
                'clientMessageId' => $m->client_message_id,
                'senderId' => $m->sender_id,
                'isOwn' => ($m->sender_id === $userId),
                'message' => $m->message,
                'attachments' => $m->attachments,
                'deliveredAt' => $m->delivered_at?->toISOString(),
                'readAt' => $m->read_at?->toISOString(),
                'createdAt' => $m->created_at->toISOString(),
            ];
        });

        return $this->paginated($messages, $items, 'Messages history retrieved');
    }

    /**
     * POST /api/v1/conversations/{id}/messages
     */
    public function sendMessage(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'client_message_id' => 'nullable|string',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;
        $conversation = Conversation::find($id);

        if (!$conversation || ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId)) {
            return $this->error('Conversation not found or access denied', 'FORBIDDEN', 403);
        }

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'client_message_id' => $request->client_message_id,
            'message' => $request->message,
            'attachments' => $request->attachments,
            'delivered_at' => now(),
        ]);

        $conversation->update([
            'last_message' => $request->message,
            'last_message_at' => now(),
        ]);

        return $this->success([
            'id' => $msg->id,
            'clientMessageId' => $msg->client_message_id,
            'senderId' => $msg->sender_id,
            'message' => $msg->message,
            'attachments' => $msg->attachments,
            'deliveredAt' => $msg->delivered_at->toISOString(),
            'createdAt' => $msg->created_at->toISOString(),
        ], 'Message dispatched', [], 201);
    }

    /**
     * PUT /api/v1/conversations/{id}/read
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        ChatMessage::where('conversation_id', $id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->success(['readAt' => now()->toISOString()], 'Read cursor updated');
    }

    /**
     * POST /api/v1/conversations/{id}/delivered
     */
    public function markAsDelivered(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        ChatMessage::where('conversation_id', $id)
            ->where('sender_id', '!=', $userId)
            ->whereNull('delivered_at')
            ->update(['delivered_at' => now()]);

        return $this->success(['deliveredAt' => now()->toISOString()], 'Delivery cursor updated');
    }
}
