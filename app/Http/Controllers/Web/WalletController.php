<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinPackage;
use App\Models\CoinTransaction;
use App\Models\User;
use App\Models\CreatorSubscription;
use App\Services\TaxService;

class WalletController extends Controller
{
    public function coins()
    {
        $packages = CoinPackage::where('is_active', true)->get()->map(function ($pkg) {
            $hst = TaxService::calculateHst($pkg->price_usd);
            $pkg->hst_tax = $hst;
            $pkg->total_price_with_tax = round($pkg->price_usd + $hst, 2);
            return $pkg;
        });

        $user = auth()->user();
        $transactions = $user ? CoinTransaction::where('user_id', $user->id)->orderBy('id', 'desc')->take(10)->get() : collect();

        return view('wallet.coins', compact('packages', 'transactions'));
    }

    public function buyCoins(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $package = CoinPackage::findOrFail($request->package_id);
        $user = auth()->user();

        $hstTax = TaxService::calculateHst($package->price_usd);
        $totalCoins = $package->total_coins;

        $user->coin_balance += $totalCoins;
        if ($package->badge_tier) {
            $user->is_vip = true;
            $user->vip_badge_tier = $package->badge_tier;
        }
        $user->save();

        CoinTransaction::create([
            'user_id' => $user->id,
            'type' => 'purchase',
            'amount_coins' => $totalCoins,
            'amount_usd' => $package->price_usd,
            'hst_tax_usd' => $hstTax,
            'payment_method' => $request->payment_method ?? 'card',
            'reference_id' => 'PKG-' . $package->id . '-' . time(),
            'description' => "Purchased {$package->name} ({$totalCoins} Coins)",
        ]);

        return redirect()->route('wallet.coins')->with('success', "Added {$totalCoins} coins to your balance!");
    }

    public function subscribe($creatorId = null)
    {
        $creator = null;
        if ($creatorId) {
            $creator = User::where('role', 'creator')->with('creatorProfile')->find($creatorId);
        }

        if (!$creator) {
            $creator = User::where('role', 'creator')->with('creatorProfile')->first();
        }

        return view('wallet.subscribe', compact('creator'));
    }

    public function processSubscription(Request $request, $creatorId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $creator = User::findOrFail($creatorId);

        CreatorSubscription::updateOrCreate(
            ['subscriber_id' => $user->id, 'creator_id' => $creator->id],
            [
                'monthly_price' => TaxService::CREATOR_SUBSCRIPTION_PRICE,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
            ]
        );

        return redirect()->route('wallet.subscribe', ['creatorId' => $creator->id])->with('success', "Subscribed to {$creator->name} for $7.99/month!");
    }
}
