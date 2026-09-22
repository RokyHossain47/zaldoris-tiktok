<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PkBattle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PkBattleController extends BaseApiController
{
    /**
     * GET /api/v1/pk-battles/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $battle = PkBattle::with(['stream.host', 'hostUser', 'challengerUser'])->find($id);
        if (!$battle) {
            $battle = PkBattle::with(['stream.host', 'hostUser', 'challengerUser'])->first();
        }

        if (!$battle) {
            return $this->error('PK Battle not found', 'NOT_FOUND', 404);
        }

        $hostUser = $battle->hostUser ?: ($battle->host1 ?: $battle->stream?->host);
        $challenger = $battle->challengerUser ?: $battle->host2;

        $hostScore = $battle->host1_score ?? ($battle->host_points ?? 0);
        $challengerScore = $battle->host2_score ?? ($battle->challenger_points ?? 0);
        $startsAt = $battle->starts_at ?? $battle->started_at;
        $winnerId = $battle->winner_id ?? $battle->winner_user_id;

        return $this->success([
            'battleId' => $battle->id,
            'status' => $battle->status,
            'round' => (int)($battle->current_round ?: 1),
            'durationSeconds' => (int)$battle->duration_seconds,
            'startedAt' => $startsAt?->toISOString(),
            'endsAt' => $battle->ends_at?->toISOString(),
            'serverTime' => now()->toISOString(),
            'participants' => [
                'teamA' => [
                    'userId' => $hostUser?->id,
                    'name' => $hostUser?->name ?: 'Host Star',
                    'username' => $hostUser?->username,
                    'avatarUrl' => $hostUser?->avatar_url,
                    'score' => (int)$hostScore,
                    'streamUrl' => $battle->stream?->stream_url ?: 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                ],
                'teamB' => [
                    'userId' => $challenger?->id,
                    'name' => $challenger?->name ?: 'Challenger Star',
                    'username' => $challenger?->username,
                    'avatarUrl' => $challenger?->avatar_url,
                    'score' => (int)$challengerScore,
                    'streamUrl' => $battle->stream2?->stream_url ?: 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8',
                ]
            ],
            'winnerId' => $winnerId,
            'scoringPolicy' => [
                'giftCoinMultiplier' => 1,
                'tapScoreCapPerSecond' => 10,
            ]
        ], 'PK Battle snapshot retrieved');
    }
}
