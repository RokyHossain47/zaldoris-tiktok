<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Gift;
use App\Models\GiftTransaction;
use App\Models\CoinTransaction;
use App\Models\User;
use App\Models\Stream;
use App\Models\PkBattle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GiftController extends BaseApiController
{
    /**
     * GET /api/v1/gifts
     */
    public function index(): JsonResponse
    {
        $gifts = Gift::where('is_active', true)
            ->orderBy('coin_cost', 'asc')
            ->get();

        $items = $gifts->map(function($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'coinCost' => (int)$g->coin_cost,
                'iconUrl' => $g->icon_url,
                'animationUrl' => $g->animation_url,
                'durationSeconds' => (int)($g->duration_seconds ?: 3),
                'isSpecial' => (bool)$g->is_special,
            ];
        });

        return $this->success($items, 'Gift catalog retrieved');
    }

    /**
     * POST /api/v1/gift-sends
     * Atomic balance validation, debit coins, credit creator, and record transaction
     */
    public function sendGift(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'gift_id' => 'required|exists:gifts,id',
            'recipient_id' => 'required|exists:users,id',
            'stream_id' => 'nullable|exists:streams,id',
            'pk_battle_id' => 'nullable|exists:pk_battles,id',
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $sender = $request->user();
        $gift = Gift::find($request->gift_id);
        $recipient = User::find($request->recipient_id);
        $quantity = (int) $request->input('quantity', 1);

        $totalCoinsCost = (int) ($gift->coin_cost * $quantity);

        if ($sender->coin_balance < $totalCoinsCost) {
            return $this->error("Insufficient coin balance. You need {$totalCoinsCost} coins, but have {$sender->coin_balance}.", 'INSUFFICIENT_COINS', 400, [
                'requiredCoins' => $totalCoinsCost,
                'availableCoins' => (int)$sender->coin_balance,
            ]);
        }

        return DB::transaction(function() use ($sender, $recipient, $gift, $quantity, $totalCoinsCost, $request) {
            // Debit sender
            $sender->coin_balance -= $totalCoinsCost;
            $sender->save();

            // Credit recipient creator
            $recipient->coin_balance += $totalCoinsCost;
            $recipient->earnings_usd += round($totalCoinsCost * 0.005, 2); // 50% revenue share
            $recipient->save();

            if ($recipient->creatorProfile) {
                $recipient->creatorProfile->increment('total_coins_received', $totalCoinsCost);
            }

            // Record Gift Transaction
            $gt = GiftTransaction::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'stream_id' => $request->stream_id,
                'gift_id' => $gift->id,
                'coin_cost' => $totalCoinsCost,
            ]);

            // Ledger record for sender
            CoinTransaction::create([
                'user_id' => $sender->id,
                'type' => 'gift_sent',
                'amount' => -$totalCoinsCost,
                'balance_after' => $sender->coin_balance,
                'description' => "Sent {$quantity}x {$gift->name} to {$recipient->name}",
                'reference_id' => 'GFT-' . strtoupper(Str::random(10)),
            ]);

            // Ledger record for recipient
            CoinTransaction::create([
                'user_id' => $recipient->id,
                'type' => 'gift_received',
                'amount' => $totalCoinsCost,
                'balance_after' => $recipient->coin_balance,
                'description' => "Received {$quantity}x {$gift->name} from {$sender->name}",
                'reference_id' => 'GFT-' . strtoupper(Str::random(10)),
            ]);

            // If in PK battle, update team score atomically
            if ($request->pk_battle_id) {
                $battle = PkBattle::find($request->pk_battle_id);
                if ($battle) {
                    if ($battle->host_user_id === $recipient->id) {
                        $battle->increment('host_points', $totalCoinsCost);
                    } elseif ($battle->challenger_user_id === $recipient->id) {
                        $battle->increment('challenger_points', $totalCoinsCost);
                    }
                }
            }

            return $this->success([
                'transactionId' => $gt->id,
                'gift' => [
                    'id' => $gift->id,
                    'name' => $gift->name,
                    'animationUrl' => $gift->animation_url,
                    'durationSeconds' => (int)($gift->duration_seconds ?: 3),
                ],
                'quantity' => $quantity,
                'totalCoinsDebited' => $totalCoinsCost,
                'resultingBalance' => (int)$sender->coin_balance,
                'recipient' => [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                ],
            ], "{$quantity}x {$gift->name} sent successfully to {$recipient->name}", [], 201);
        });
    }

    /**
     * GET /api/v1/me/gifts
     */
    public function myGifts(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $type = $request->input('type', 'sent'); // 'sent', 'received'

        if ($type === 'received') {
            $txns = GiftTransaction::with(['gift', 'sender'])
                ->where('recipient_id', $userId)
                ->latest()
                ->paginate($request->input('limit', 20));
        } else {
            $txns = GiftTransaction::with(['gift', 'recipient'])
                ->where('sender_id', $userId)
                ->latest()
                ->paginate($request->input('limit', 20));
        }

        $items = collect($txns->items())->map(function($g) use ($type) {
            return [
                'id' => $g->id,
                'giftName' => $g->gift?->name ?: 'Gift',
                'giftIcon' => $g->gift?->icon_url,
                'coinCost' => (int)$g->coin_cost,
                'partner' => [
                    'id' => $type === 'received' ? $g->sender?->id : $g->recipient?->id,
                    'name' => $type === 'received' ? $g->sender?->name : $g->recipient?->name,
                    'avatarUrl' => $type === 'received' ? $g->sender?->avatar_url : $g->recipient?->avatar_url,
                ],
                'createdAt' => $g->created_at->toISOString(),
            ];
        });

        return $this->paginated($txns, $items, 'Gifts history retrieved');
    }
}
