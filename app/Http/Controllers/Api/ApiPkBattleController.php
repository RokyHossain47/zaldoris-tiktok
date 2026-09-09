<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PkBattle;
use App\Models\Stream;
use App\Models\Gift;
use App\Models\GiftTransaction;
use App\Models\CoinTransaction;
use App\Services\TaxService;
use App\Services\FraudDetectionService;
use Illuminate\Support\Facades\DB;

class ApiPkBattleController extends Controller
{
    /**
     * Get Active PK Battle.
     */
    public function show($id)
    {
        $battle = PkBattle::with(['host1', 'host2', 'stream1', 'stream2', 'winner'])
            ->findOrFail($id);

        $totalScore = $battle->host1_score + $battle->host2_score;
        $host1Pct = $totalScore > 0 ? round(($battle->host1_score / $totalScore) * 100, 1) : 50;
        $host2Pct = 100 - $host1Pct;

        return response()->json([
            'success' => true,
            'battle' => $battle,
            'score_percentages' => [
                'host1_percentage' => $host1Pct,
                'host2_percentage' => $host2Pct,
            ],
            'time_remaining_seconds' => $battle->ends_at ? max(0, now()->diffInSeconds($battle->ends_at, false)) : 0,
        ]);
    }

    /**
     * Send Gift to Creator during PK Battle (Updates score bar + wallet + 60% creator share).
     */
    public function sendBattleGift(Request $request, $id)
    {
        $battle = PkBattle::findOrFail($id);
        $user = $request->user();

        $validated = $request->validate([
            'gift_id' => 'required|exists:gifts,id',
            'target_host_id' => 'required|in:' . $battle->host1_id . ',' . $battle->host2_id,
        ]);

        $gift = Gift::findOrFail($validated['gift_id']);
        $targetHostId = (int) $validated['target_host_id'];

        if ($user->coin_balance < $gift->coin_cost) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient coin balance. Please top up your coins.',
                'required_coins' => $gift->coin_cost,
                'current_balance' => $user->coin_balance,
            ], 400);
        }

        // Calculate USD value of coins: 70 coins ~ $1.00 => $0.0142 per coin
        $usdValue = round($gift->coin_cost * 0.0142, 2);

        // Check wash trading and velocity rules (SRS #13)
        $fraudService = new FraudDetectionService();
        $fraudCheck = $fraudService->checkGiftVelocity($user, $targetHostId, $usdValue);

        return DB::transaction(function () use ($battle, $user, $gift, $targetHostId, $usdValue, $fraudCheck) {
            // 1. Deduct coins from sender
            $user->coin_balance -= $gift->coin_cost;
            $user->save();

            CoinTransaction::create([
                'user_id' => $user->id,
                'type' => 'gift_sent',
                'amount_coins' => -$gift->coin_cost,
                'description' => "Sent {$gift->name} in PK Battle #{$battle->id}",
            ]);

            // 2. Compute 60% Creator Revenue Share (SRS Page 4)
            $creatorShare = round($usdValue * TaxService::CREATOR_GIFT_REVENUE_SHARE, 2);
            $platformCommission = round($usdValue - $creatorShare, 2);

            $targetUser = \App\Models\User::find($targetHostId);
            $targetUser->earnings_usd += $creatorShare;
            $targetUser->save();

            // 3. Record Gift Transaction
            $gt = GiftTransaction::create([
                'stream_id' => $battle->stream1_id,
                'sender_id' => $user->id,
                'receiver_id' => $targetHostId,
                'gift_id' => $gift->id,
                'coin_amount' => $gift->coin_cost,
                'creator_earning_usd' => $creatorShare,
                'platform_commission_usd' => $platformCommission,
            ]);

            // 4. Update Battle Score
            if ($targetHostId === $battle->host1_id) {
                $battle->host1_score += $gift->coin_cost;
            } else {
                $battle->host2_score += $gift->coin_cost;
            }
            $battle->save();

            $totalScore = $battle->host1_score + $battle->host2_score;
            $host1Pct = $totalScore > 0 ? round(($battle->host1_score / $totalScore) * 100, 1) : 50;

            return response()->json([
                'success' => true,
                'message' => "Sent {$gift->name}!",
                'gift' => $gift,
                'updated_scores' => [
                    'host1_score' => $battle->host1_score,
                    'host2_score' => $battle->host2_score,
                    'host1_percentage' => $host1Pct,
                    'host2_percentage' => 100 - $host1Pct,
                ],
                'new_coin_balance' => $user->coin_balance,
                'fraud_warning' => $fraudCheck['warning'] ?? null,
            ]);
        });
    }
}
