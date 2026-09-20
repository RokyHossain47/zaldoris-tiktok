<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentMethodController extends BaseApiController
{
    /**
     * GET /api/v1/me/payment-methods
     */
    public function index(Request $request): JsonResponse
    {
        $methods = PaymentMethod::where('user_id', $request->user()->id)
            ->orderBy('is_default', 'desc')
            ->get();

        return $this->success($methods, 'Saved payment methods retrieved');
    }

    /**
     * POST /api/v1/payment-method-setup-sessions
     */
    public function createSetupSession(Request $request): JsonResponse
    {
        $user = $request->user();
        $sessionId = 'seti_' . Str::random(24);
        $clientSecret = $sessionId . '_secret_' . Str::random(24);

        return $this->success([
            'setupSessionId' => $sessionId,
            'clientSecret' => $clientSecret,
            'publishableKey' => setting('stripe_key', 'pk_test_sample_zaldoris'),
            'supportedProviders' => ['stripe', 'paypal', 'apple_pay', 'google_pay'],
        ], 'Payment method setup session created');
    }

    /**
     * POST /api/v1/me/payment-methods
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'nullable|string',
            'token' => 'required|string',
            'type' => 'nullable|string|in:card,paypal,apple_pay,google_pay',
            'brand' => 'nullable|string', // visa, mastercard, amex
            'last_four' => 'nullable|string|size:4',
            'expiry_month' => 'nullable|string|max:2',
            'expiry_year' => 'nullable|string|max:4',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;

        if ($request->boolean('is_default')) {
            PaymentMethod::where('user_id', $userId)->update(['is_default' => false]);
        }

        $hasMethods = PaymentMethod::where('user_id', $userId)->exists();
        $isDefault = $request->boolean('is_default') || !$hasMethods;

        $method = PaymentMethod::create([
            'user_id' => $userId,
            'provider' => $request->input('provider', 'stripe'),
            'token' => $request->token,
            'type' => $request->input('type', 'card'),
            'brand' => strtolower($request->input('brand', 'visa')),
            'last_four' => $request->input('last_four', '4242'),
            'expiry_month' => $request->input('expiry_month', '12'),
            'expiry_year' => $request->input('expiry_year', '2028'),
            'is_default' => $isDefault,
            'supported_contexts' => ['direct_shop', 'auction_bid_hold', 'coin_purchase', 'creator_subscription'],
        ]);

        return $this->success($method, 'Payment method attached successfully', [], 201);
    }

    /**
     * PATCH /api/v1/me/payment-methods/{id}
     */
    public function setDefault(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        $method = PaymentMethod::where('id', $id)->where('user_id', $userId)->first();

        if (!$method) {
            return $this->error('Payment method not found', 'NOT_FOUND', 404);
        }

        PaymentMethod::where('user_id', $userId)->update(['is_default' => false]);
        $method->update(['is_default' => true]);

        return $this->success($method, 'Default payment method set');
    }

    /**
     * DELETE /api/v1/me/payment-methods/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $userId = $request->user()->id;
        PaymentMethod::where('id', $id)->where('user_id', $userId)->delete();

        return $this->success(null, 'Payment method removed');
    }
}
