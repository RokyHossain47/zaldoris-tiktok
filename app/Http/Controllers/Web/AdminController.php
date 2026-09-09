<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Stream;
use App\Models\Order;
use App\Models\Product;
use App\Models\FraudAlert;
use App\Models\Dispute;
use App\Models\AiModerationLog;
use App\Models\Advertisement;
use App\Models\SellerProfile;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalCreators = User::where('role', 'creator')->count();
        $activeStreams = Stream::where('is_live', true)->count();
        $totalOrders = Order::count();
        $totalSalesUsd = Order::where('payment_status', '!=', 'refunded')->sum('total_amount');
        $activeFraudAlerts = FraudAlert::where('status', 'pending')->count();
        $openDisputes = Dispute::where('status', 'open')->count();

        $recentFraudAlerts = FraudAlert::with('user')->orderBy('id', 'desc')->take(5)->get();
        $recentDisputes = Dispute::with(['buyer', 'seller', 'order'])->orderBy('id', 'desc')->take(5)->get();
        $recentModeration = AiModerationLog::orderBy('id', 'desc')->take(5)->get();

        return view('admin.index', compact(
            'totalUsers',
            'totalSellers',
            'totalCreators',
            'activeStreams',
            'totalOrders',
            'totalSalesUsd',
            'activeFraudAlerts',
            'openDisputes',
            'recentFraudAlerts',
            'recentDisputes',
            'recentModeration'
        ));
    }

    public function fraud()
    {
        $alerts = FraudAlert::with('user')->orderBy('id', 'desc')->paginate(15);
        return view('admin.fraud', compact('alerts'));
    }

    public function disputes()
    {
        $disputes = Dispute::with(['buyer', 'seller', 'order'])->orderBy('id', 'desc')->paginate(15);
        return view('admin.disputes', compact('disputes'));
    }

    public function moderation()
    {
        $logs = AiModerationLog::with('user')->orderBy('id', 'desc')->paginate(15);
        return view('admin.moderation', compact('logs'));
    }

    public function ads()
    {
        $ads = Advertisement::all();
        return view('admin.ads', compact('ads'));
    }

    public function toggleAd($id)
    {
        $ad = Advertisement::findOrFail($id);
        $ad->is_active = !$ad->is_active;
        $ad->save();

        return back()->with('success', "Ad '{$ad->title}' status updated.");
    }

    public function resolveDispute(Request $request, $id)
    {
        $dispute = Dispute::findOrFail($id);
        $resolution = $request->input('resolution', 'resolved_refund'); // resolved_refund, resolved_credit, rejected

        $dispute->status = $resolution;
        $dispute->resolution_notes = $request->input('notes', 'Resolved by Admin compliance team.');
        $dispute->resolved_at = now();
        $dispute->resolved_by = auth()->id();
        $dispute->save();

        return back()->with('success', 'Dispute resolution recorded.');
    }
}
