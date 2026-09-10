<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Stream;
use App\Models\Order;
use App\Models\Product;
use App\Models\FraudAlert;
use App\Models\Dispute;
use App\Models\AiModerationLog;
use App\Models\Advertisement;
use App\Models\SellerProfile;
use App\Models\CreatorProfile;
use App\Models\CoinTransaction;

class AdminController extends Controller
{
    /**
     * Show dedicated Admin Login page.
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Process Admin Login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized. Only administrators can access this portal.']);
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
        }

        return back()->withErrors(['email' => 'Invalid admin credentials.'])->onlyInput('email');
    }

    /**
     * Admin Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out from Admin Console.');
    }

    /**
     * Main Admin Dashboard (Image Layout Match).
     */
    public function dashboard(Request $request)
    {
        // 8 Metric Cards
        $totalUsers = User::count();
        $activeUsers = User::where('is_suspended', false)->count();
        $liveStreamers = Stream::where('is_live', true)->count();
        $vipMembers = User::where('is_vip', true)->count();
        $activeHosts = User::whereIn('role', ['creator', 'seller'])->count();
        $agencies = SellerProfile::count();
        $feedPosts = Stream::count() + Product::count();
        $reportedUsers = FraudAlert::where('status', 'pending')->count();

        // User Management Filter & Search
        $filter = $request->query('filter', 'all'); // all, active, blocked, hosts
        $search = $request->query('search', '');

        $usersQuery = User::with(['sellerProfile', 'creatorProfile']);

        if ($filter === 'active') {
            $usersQuery->where('is_suspended', false);
        } elseif ($filter === 'blocked') {
            $usersQuery->where('is_suspended', true);
        } elseif ($filter === 'hosts') {
            $usersQuery->whereIn('role', ['creator', 'seller']);
        }

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $usersQuery->orderBy('id', 'desc')->paginate(15);

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'liveStreamers',
            'vipMembers',
            'activeHosts',
            'agencies',
            'feedPosts',
            'reportedUsers',
            'users',
            'filter',
            'search'
        ));
    }

    /**
     * Dedicated User Management Page.
     */
    public function users(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all, active, blocked, hosts, vip
        $role = $request->query('role', '');
        $search = $request->query('search', '');

        $query = User::with(['sellerProfile', 'creatorProfile']);

        if ($filter === 'active') {
            $query->where('is_suspended', false);
        } elseif ($filter === 'blocked' || $filter === 'banned') {
            $query->where('is_suspended', true);
        } elseif ($filter === 'hosts') {
            $query->whereIn('role', ['creator', 'seller']);
        } elseif ($filter === 'vip') {
            $query->where('is_vip', true);
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalUsers = User::count();
        $activeUsers = User::where('is_suspended', false)->count();
        $bannedUsers = User::where('is_suspended', true)->count();
        $vipUsers = User::where('is_vip', true)->count();

        return view('admin.users.index', compact(
            'users',
            'filter',
            'role',
            'search',
            'totalUsers',
            'activeUsers',
            'bannedUsers',
            'vipUsers'
        ));
    }

    /**
     * Create / Store New User.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:191|unique:users,username|regex:/^[a-zA-Z0-9._]+$/',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
            'role' => 'required|in:buyer,creator,seller,moderator,admin,dispute_manager',
            'coin_balance' => 'nullable|integer|min:0',
            'is_vip' => 'nullable',
            'is_verified' => 'nullable',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => strtolower($validated['username']),
            'email' => strtolower($validated['email']),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'coin_balance' => $validated['coin_balance'] ?? 0,
            'is_vip' => $request->boolean('is_vip'),
            'is_verified' => $request->boolean('is_verified'),
            'email_verified_at' => now(),
        ]);

        if ($user->role === 'seller') {
            SellerProfile::firstOrCreate(['user_id' => $user->id], [
                'business_name' => $user->name . "'s Shop",
                'is_verified' => true,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    /**
     * Update Existing User.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'username' => 'required|string|max:191|regex:/^[a-zA-Z0-9._]+$/|unique:users,username,' . $id,
            'email' => 'required|email|max:191|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:buyer,creator,seller,moderator,admin,dispute_manager',
            'coin_balance' => 'required|integer|min:0',
            'is_vip' => 'nullable',
            'is_verified' => 'nullable',
            'is_suspended' => 'nullable',
        ]);

        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = strtolower($validated['email']);
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->coin_balance = $validated['coin_balance'];
        $user->is_vip = $request->boolean('is_vip');
        $user->is_verified = $request->boolean('is_verified');

        if (!($user->isAdmin() && $user->id === auth()->id())) {
            $user->is_suspended = $request->boolean('is_suspended');
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Delete User.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own logged-in admin account.']);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User '{$name}' has been permanently deleted.");
    }

    /**
     * Toggle User Block/Suspension status.
     */
    public function toggleUserBlock($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() && $user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot block your own super admin account.']);
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $status = $user->is_suspended ? 'suspended/banned' : 'unbanned/active';
        return back()->with('success', "User #{$user->id} ({$user->name}) is now {$status}.");
    }

    /**
     * Manage / Adjust User Coins balance.
     */
    public function updateUserCoins(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'coin_balance' => 'required|integer|min:0',
        ]);

        $oldCoins = $user->coin_balance;
        $diff = $validated['coin_balance'] - $oldCoins;
        $user->coin_balance = $validated['coin_balance'];
        $user->save();

        CoinTransaction::create([
            'user_id' => $user->id,
            'type' => 'bonus',
            'amount_coins' => $diff,
            'amount_usd' => 0.00,
            'description' => "Coins adjusted by Administrator.",
        ]);

        return back()->with('success', "Updated coins for {$user->name} to {$user->coin_balance} coins.");
    }

    /**
     * Update User Role.
     */
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:buyer,creator,seller,moderator,admin,dispute_manager',
        ]);

        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', "Updated {$user->name}'s role to {$user->role}.");
    }

    /**
     * Banners & Ads Management.
     */
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



    /**
     * Disputes Management.
     */
    public function disputes()
    {
        $disputes = Dispute::with(['buyer', 'seller', 'order'])->orderBy('id', 'desc')->paginate(15);
        return view('admin.disputes', compact('disputes'));
    }

    public function resolveDispute(Request $request, $id)
    {
        $dispute = Dispute::findOrFail($id);
        $resolution = $request->input('resolution', 'resolved_refund');

        $dispute->status = $resolution;
        $dispute->resolution_notes = $request->input('notes', 'Resolved by Admin compliance team.');
        $dispute->resolved_at = now();
        $dispute->resolved_by = auth()->id();
        $dispute->save();

        return back()->with('success', 'Dispute resolution recorded.');
    }
}
