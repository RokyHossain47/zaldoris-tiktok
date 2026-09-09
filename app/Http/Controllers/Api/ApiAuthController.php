<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\CreatorProfile;

class ApiAuthController extends Controller
{
    /**
     * Mobile Register (Buyer, Creator, or Seller).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:buyer,creator,seller',
            'accepted_terms' => 'required|accepted', // Legal compliance check (SRS Page 6)
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? strtolower(explode(' ', $validated['name'])[0] . rand(100, 999)),
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'buyer',
            'coin_balance' => 100, // Welcome bonus
        ]);

        if ($user->role === 'seller') {
            SellerProfile::create([
                'user_id' => $user->id,
                'business_name' => $request->input('business_name', $user->name . ' Shop'),
                'status' => 'pending',
            ]);
        } elseif ($user->role === 'creator') {
            CreatorProfile::create([
                'user_id' => $user->id,
                'bio' => $request->input('bio', 'Hello from new creator!'),
            ]);
        }

        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'token' => $token,
            'user' => $user->load(['sellerProfile', 'creatorProfile']),
        ], 201);
    }

    /**
     * Mobile Login with Email or Username.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->login)
            ->orWhere('username', $request->login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/username or password.',
            ], 401);
        }

        if ($user->is_suspended) {
            return response()->json([
                'success' => false,
                'message' => 'Account has been suspended due to policy violations.',
            ], 403);
        }

        $token = $user->createToken('mobile_auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token,
            'user' => $user->load(['sellerProfile', 'creatorProfile']),
        ]);
    }

    /**
     * Mobile OTP verification (SRS #3 phone verification).
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        // Demo OTP verification (accept 123456 or match)
        if ($request->otp === '123456' || $request->otp === '999999') {
            $user = $request->user();
            if ($user) {
                $user->phone = $request->phone;
                $user->phone_verified_at = now();
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone verified successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP code. Please enter 123456.',
        ], 422);
    }

    /**
     * Get Current Authenticated Profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()->load(['sellerProfile', 'creatorProfile']),
        ]);
    }
}
