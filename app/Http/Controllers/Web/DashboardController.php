<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stream;
use App\Models\Auction;
use App\Models\GiftTransaction;
use App\Models\CreatorSubscription;

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
     * Creator Dashboard (SRS Page 1 & Page 4, 5).
     */
    public function creator()
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

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

        // Revenue Breakdown (SRS Page 5)
        $grossGiftsUsd = $giftsReceived->sum('creator_earning_usd') / 0.60;
        $appStoreFees = round($grossGiftsUsd * 0.15, 2); // 15% app store fee
        $hstTaxes = round($grossGiftsUsd * 0.13, 2); // 13% HST
        $platformCut = round($grossGiftsUsd * 0.25, 2);
        $creatorNetPayout = $user->earnings_usd;

        return view('dashboard.creator', compact(
            'user',
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
}
