<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stream;
use App\Models\Auction;
use App\Models\GiftTransaction;
use App\Models\CreatorSubscription;
use App\Models\UserAddress;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Seller Dashboard (SRS Page 1 & Page 12).
     */
    public function seller()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $orders = Order::with(['items.product', 'buyer'])
            ->where('seller_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $products = Product::where('seller_id', $user->id)->get();
        $auctions = Auction::where('seller_id', $user->id)->get();
        $streams = Stream::where('host_id', $user->id)->orderBy('id', 'desc')->get();
        $profile = $user->sellerProfile;

        $pendingDispatchCount = $orders->where('status', 'packing')->count();
        $completedSalesTotal = $orders->where('status', 'delivered')->sum('subtotal');

        return view('dashboard.seller', compact(
            'user',
            'orders',
            'products',
            'auctions',
            'streams',
            'profile',
            'pendingDispatchCount',
            'completedSalesTotal'
        ));
    }

    /**
     * User Panel & Creator Studio (Unified Hub).
     */
    public function creator()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        // Buyer Orders
        $buyerOrders = Order::with(['items', 'seller'])
            ->where('buyer_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        // User Shipping Addresses
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        // Following Creators
        $following = $user->following()->with(['creatorProfile', 'sellerProfile'])->get();

        // Creator Studio Data
        $streams = Stream::where('host_id', $user->id)->orderBy('id', 'desc')->get();
        $giftsReceived = GiftTransaction::with(['gift', 'sender'])
            ->where('receiver_id', $user->id)
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        $subscribers = CreatorSubscription::with('subscriber')
            ->where('creator_id', $user->id)
            ->where('status', 'active')
            ->get();

        $profile = $user->creatorProfile;

        // Revenue Breakdown
        $grossGiftsUsd = $giftsReceived->sum('creator_earning_usd') > 0 
            ? ($giftsReceived->sum('creator_earning_usd') / 0.60) 
            : 0.00;
        $appStoreFees = round($grossGiftsUsd * 0.15, 2);
        $hstTaxes = round($grossGiftsUsd * 0.13, 2);
        $platformCut = round($grossGiftsUsd * 0.25, 2);
        $creatorNetPayout = (float)$user->earnings_usd;

        return view('dashboard.creator', compact(
            'user',
            'buyerOrders',
            'addresses',
            'following',
            'streams',
            'giftsReceived',
            'subscribers',
            'profile',
            'grossGiftsUsd',
            'appStoreFees',
            'hstTaxes',
            'platformCut',
            'creatorNetPayout'
        ));
    }

    /**
     * Update User Profile (Name, Phone, Avatar).
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? $user->phone;

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $user->avatar = 'uploads/avatars/' . $filename;
        }

        $user->save();

        return redirect()->route('dashboard.creator', ['tab' => 'profile'])->with('success', 'Profile updated successfully!');
    }

    /**
     * Update User Password.
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('dashboard.creator', ['tab' => 'profile'])->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('dashboard.creator', ['tab' => 'profile'])->with('success', 'Password changed successfully!');
    }

    /**
     * Store New Shipping Address.
     */
    public function storeAddress(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $hasAddresses = $user->addresses()->exists();
        $isDefault = $request->boolean('is_default') || !$hasAddresses;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = UserAddress::create([
            'user_id' => $user->id,
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'region' => $validated['region'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
            'is_default' => $isDefault,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipping address saved successfully!',
                'address' => $address
            ]);
        }

        return redirect()->route('dashboard.creator', ['tab' => 'addresses'])->with('success', 'Shipping address added successfully!');
    }

    /**
     * Update Existing Shipping Address.
     */
    public function updateAddress(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $address = UserAddress::where('user_id', $user->id)->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');
        if ($isDefault) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update([
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'region' => $validated['region'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
            'is_default' => $isDefault || $address->is_default,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shipping address updated successfully!',
                'address' => $address
            ]);
        }

        return redirect()->route('dashboard.creator', ['tab' => 'addresses'])->with('success', 'Shipping address updated successfully!');
    }

    /**
     * Delete Shipping Address.
     */
    public function deleteAddress($id)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $address = UserAddress::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $next = $user->addresses()->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return redirect()->route('dashboard.creator', ['tab' => 'addresses'])->with('success', 'Shipping address removed.');
    }

    /**
     * Set Address as Default.
     */
    public function setDefaultAddress($id)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $user->addresses()->update(['is_default' => false]);
        $user->addresses()->where('id', $id)->update(['is_default' => true]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Default shipping address updated.'
            ]);
        }

        return redirect()->route('dashboard.creator', ['tab' => 'addresses'])->with('success', 'Default address updated.');
    }

    /**
     * Toggle Follow / Unfollow User.
     */
    public function toggleFollow($userId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please log in to follow creators.'], 401);
        }

        $targetUser = User::findOrFail($userId);
        $isFollowing = $user->following()->where('following_id', $targetUser->id)->exists();

        if ($isFollowing) {
            $user->following()->detach($targetUser->id);
            $following = false;
            $message = "Unfollowed {$targetUser->name}.";
        } else {
            $user->following()->attach($targetUser->id);
            $following = true;
            $message = "You are now following {$targetUser->name}!";
        }

        return response()->json([
            'success' => true,
            'following' => $following,
            'message' => $message,
            'follower_count' => $targetUser->followers()->count()
        ]);
    }
}

