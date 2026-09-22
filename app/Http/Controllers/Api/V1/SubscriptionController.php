<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\CreatorSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseApiController
{
    /**
     * GET /api/v1/creators/{id}/subscription-plans
     */
    public function getPlans(Request $request, $id): JsonResponse
    {
        $creator = User::with('creatorProfile')->find($id);
        if (!$creator) {
            return $this->error('Creator not found', 'NOT_FOUND', 404);
        }

        $plans = [
            [
                'id' => 'tier_fan',
                'name' => 'VIP Member Tier 1',
                'billingInterval' => 'monthly',
                'price' => 4.99,
                'priceMinor' => 499,
                'currency' => setting('currency_code', 'CAD'),
                'perks' => [
                    'Exclusive Creator Badge in live streams',
                    'Custom animated emotes in live chat',
                    'Subscriber-only live auctions access',
                    'Direct messaging priority'
                ]
            ],
            [
                'id' => 'tier_supporter',
                'name' => 'Super VIP Member Tier 2',
                'billingInterval' => 'monthly',
                'price' => 9.99,
                'priceMinor' => 999,
                'currency' => setting('currency_code', 'CAD'),
                'perks' => [
                    'All Tier 1 VIP Perks included',
                    'Free monthly 100 bonus coins drop',
                    'Priority product drops allocation',
                    'Exclusive backstage streaming access'
                ]
            ]
        ];

        return $this->success([
            'creator' => [
                'id' => $creator->id,
                'name' => $creator->name,
                'avatarUrl' => $creator->avatar_url,
                'subscriberCount' => (int)($creator->creatorProfile?->subscriber_count ?: 0),
            ],
            'plans' => $plans,
        ], 'Creator subscription plans retrieved');
    }

    /**
     * POST /api/v1/subscription-quotes
     */
    public function quoteSubscription(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'creator_id' => 'required|exists:users,id',
            'plan_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $planPrice = ($request->plan_id === 'tier_supporter') ? 9.99 : 4.99;
        $tax = round($planPrice * 0.13, 2);
        $total = round($planPrice + $tax, 2);

        return $this->success([
            'quoteId' => 'SUBQ-' . strtoupper(\Illuminate\Support\Str::random(12)),
            'planPrice' => $planPrice,
            'planPriceMinor' => $this->toMinorUnits($planPrice),
            'tax' => $tax,
            'total' => $total,
            'totalMinor' => $this->toMinorUnits($total),
            'currency' => setting('currency_code', 'CAD'),
            'renewalInterval' => '1 month',
            'autoRenew' => true,
        ], 'Subscription payment quote calculated');
    }

    /**
     * POST /api/v1/subscriptions
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'creator_id' => 'required|exists:users,id',
            'plan_id' => 'nullable|string',
            'auto_renew_consent' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $user = $request->user();
        $creator = User::find($request->creator_id);

        $price = ($request->plan_id === 'tier_supporter') ? 9.99 : 4.99;

        $sub = CreatorSubscription::updateOrCreate(
            ['user_id' => $user->id, 'creator_id' => $creator->id],
            [
                'plan_name' => ($request->plan_id === 'tier_supporter') ? 'Super VIP Tier 2' : 'VIP Member Tier 1',
                'monthly_price' => $price,
                'status' => 'active',
                'auto_renew' => true,
                'started_at' => now(),
                'expires_at' => now()->addMonth(),
            ]
        );

        if ($creator->creatorProfile) {
            $creator->creatorProfile->increment('subscriber_count');
        }

        return $this->success([
            'subscriptionId' => $sub->id,
            'creator' => [
                'id' => $creator->id,
                'name' => $creator->name,
                'avatarUrl' => $creator->avatar_url,
            ],
            'plan' => $sub->plan_name,
            'status' => 'active',
            'periodEnd' => $sub->expires_at->toISOString(),
            'autoRenew' => true,
            'entitlements' => ['exclusive_badge', 'subscriber_chat', 'private_streams'],
        ], "Subscribed to {$creator->name} successfully", [], 201);
    }

    /**
     * GET /api/v1/me/subscriptions
     */
    public function mySubscriptions(Request $request): JsonResponse
    {
        $subs = CreatorSubscription::with('creator')
            ->where('user_id', $request->user()->id)
            ->get();

        $items = $subs->map(function($s) {
            return [
                'id' => $s->id,
                'creator' => [
                    'id' => $s->creator?->id,
                    'name' => $s->creator?->name,
                    'avatarUrl' => $s->creator?->avatar_url,
                ],
                'plan' => $s->plan_name,
                'price' => (float)$s->monthly_price,
                'status' => $s->status,
                'autoRenew' => (bool)$s->auto_renew,
                'startedAt' => $s->started_at?->toISOString(),
                'expiresAt' => $s->expires_at?->toISOString(),
            ];
        });

        return $this->success($items, 'Creator memberships retrieved');
    }

    /**
     * PATCH /api/v1/subscriptions/{id}
     */
    public function toggleRenewal(Request $request, $id): JsonResponse
    {
        $sub = CreatorSubscription::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$sub) {
            return $this->error('Subscription not found', 'NOT_FOUND', 404);
        }

        $newAutoRenew = $request->has('auto_renew') ? $request->boolean('auto_renew') : !$sub->auto_renew;
        $sub->update(['auto_renew' => $newAutoRenew]);

        return $this->success([
            'subscriptionId' => $sub->id,
            'autoRenew' => (bool)$sub->auto_renew,
            'status' => $sub->status,
            'periodEnd' => $sub->expires_at?->toISOString(),
        ], $newAutoRenew ? 'Auto-renewal enabled' : 'Auto-renewal cancelled');
    }
}
