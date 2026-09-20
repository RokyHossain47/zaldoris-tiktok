<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AddressController extends BaseApiController
{
    /**
     * GET /api/v1/me/addresses
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = UserAddress::where('user_id', $request->user()->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success($addresses, 'Saved addresses retrieved');
    }

    /**
     * POST /api/v1/me/addresses
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipient_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:25',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:5',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        $userId = $request->user()->id;

        if ($request->boolean('is_default')) {
            UserAddress::where('user_id', $userId)->update(['is_default' => false]);
        }

        $hasAddresses = UserAddress::where('user_id', $userId)->exists();
        $isDefault = $request->boolean('is_default') || !$hasAddresses;

        $address = UserAddress::create([
            'user_id' => $userId,
            'recipient_name' => $request->recipient_name,
            'phone' => $request->phone,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'region' => $request->region,
            'postal_code' => $request->postal_code,
            'country' => strtoupper($request->input('country', 'CA')),
            'is_default' => $isDefault,
        ]);

        return $this->success($address, 'Address saved successfully', [], 201);
    }

    /**
     * PATCH /api/v1/me/addresses/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return $this->error('Address not found', 'NOT_FOUND', 404);
        }

        $validator = Validator::make($request->all(), [
            'recipient_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:25',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:5',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error', 'VALIDATION_ERROR', 422, $validator->errors()->toArray());
        }

        if ($request->boolean('is_default')) {
            UserAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
            $address->is_default = true;
        }

        $address->fill($request->only([
            'recipient_name', 'phone', 'address_line1', 'address_line2',
            'city', 'region', 'postal_code', 'country'
        ]));
        $address->save();

        return $this->success($address, 'Address updated successfully');
    }

    /**
     * DELETE /api/v1/me/addresses/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $address = UserAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return $this->error('Address not found', 'NOT_FOUND', 404);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = UserAddress::where('user_id', $request->user()->id)->first();
            if ($next) $next->update(['is_default' => true]);
        }

        return $this->success(null, 'Address removed successfully');
    }
}
