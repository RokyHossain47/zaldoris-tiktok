<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Stream;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\FraudAlert;
use App\Models\Dispute;
use App\Models\AiModerationLog;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SellerProfile;
use App\Models\CreatorProfile;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\CoinTransaction;
use App\Models\CoinPackage;
use App\Models\Gift;
use App\Models\GiftTransaction;
use App\Models\Notification;
use App\Models\PkBattle;
use App\Models\Review;
use App\Models\CreatorSubscription;
use App\Models\Reaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
     * Banners & Ads Management (Full CRUD).
     */
     public function banners()
     {
         $banners = Advertisement::orderBy('id', 'desc')->paginate(15);
         return view('admin.banners.index', compact('banners'));
     }

     public function storeBanner(Request $request)
     {
         $validated = $request->validate([
             'title' => 'required|string|max:255',
             'subtitle' => 'nullable|string|max:255',
             'placement' => 'required|string',
             'link_url' => 'nullable|string|max:255',
             'button_text' => 'nullable|string|max:100',
             'media_url' => 'nullable|string',
             'image_file' => 'nullable|image|max:5120',
             'is_active' => 'nullable|boolean',
         ]);

         $mediaUrl = $validated['media_url'] ?? '';
         if ($request->hasFile('image_file')) {
             $file = $request->file('image_file');
             $filename = 'banner_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
             $file->move(public_path('uploads/banners'), $filename);
             $mediaUrl = asset('uploads/banners/' . $filename);
         }

         Advertisement::create([
             'title' => $validated['title'],
             'subtitle' => $validated['subtitle'] ?? null,
             'ad_type' => 'banner',
             'placement' => $validated['placement'],
             'media_url' => $mediaUrl ?: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600',
             'link_url' => $validated['link_url'] ?? '/shop',
             'button_text' => $validated['button_text'] ?? 'Learn More',
             'is_active' => $request->boolean('is_active', true),
         ]);

         return back()->with('success', 'Banner created successfully.');
     }

     public function updateBanner(Request $request, $id)
     {
         $banner = Advertisement::findOrFail($id);

         $validated = $request->validate([
             'title' => 'required|string|max:255',
             'subtitle' => 'nullable|string|max:255',
             'placement' => 'required|string',
             'link_url' => 'nullable|string|max:255',
             'button_text' => 'nullable|string|max:100',
             'media_url' => 'nullable|string',
             'image_file' => 'nullable|image|max:5120',
             'is_active' => 'nullable|boolean',
         ]);

         if ($request->hasFile('image_file')) {
             $file = $request->file('image_file');
             $filename = 'banner_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
             $file->move(public_path('uploads/banners'), $filename);
             $banner->media_url = asset('uploads/banners/' . $filename);
         } elseif (!empty($validated['media_url'])) {
             $banner->media_url = $validated['media_url'];
         }

         $banner->title = $validated['title'];
         $banner->subtitle = $validated['subtitle'] ?? null;
         $banner->placement = $validated['placement'];
         $banner->link_url = $validated['link_url'] ?? $banner->link_url;
         $banner->button_text = $validated['button_text'] ?? $banner->button_text;
         $banner->is_active = $request->boolean('is_active', true);
         $banner->save();

         return back()->with('success', 'Banner updated successfully.');
     }

     public function deleteBanner($id)
     {
         $banner = Advertisement::findOrFail($id);
         $banner->delete();

         return back()->with('success', 'Banner deleted successfully.');
     }

     public function toggleBanner($id)
     {
         $banner = Advertisement::findOrFail($id);
         $banner->is_active = !$banner->is_active;
         $banner->save();

         return back()->with('success', "Banner '{$banner->title}' status updated.");
     }

    /**
     * Categories Management (Full CRUD).
     */
    public function categories(Request $request)
    {
        $categories = Category::withCount('products')->orderBy('sort_order', 'asc')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string',
        ]);

        $imageUrl = $validated['image_url'] ?? '';
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'cat_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/categories'), $filename);
            $imageUrl = asset('uploads/categories/' . $filename);
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']),
            'icon' => $validated['icon'] ?? 'bi-tag',
            'image' => $imageUrl ?: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400',
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string',
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'cat_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/categories'), $filename);
            $category->image = asset('uploads/categories/' . $filename);
        } elseif (!empty($validated['image_url'])) {
            $category->image = $validated['image_url'];
        }

        $category->name = $validated['name'];
        if (!empty($validated['slug'])) {
            $category->slug = Str::slug($validated['slug']);
        }
        $category->icon = $validated['icon'] ?? $category->icon;
        $category->description = $validated['description'] ?? $category->description;
        $category->sort_order = $validated['sort_order'] ?? $category->sort_order;
        $category->is_active = $request->boolean('is_active', true);
        $category->save();

        return back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

    /**
     * Products Management (Full CRUD + Featured & Trending).
     */
    public function products(Request $request)
    {
        $query = Product::with(['seller', 'category']);

        if ($request->filled('filter')) {
            if ($request->filter === 'featured') {
                $query->where('is_featured', true);
            } elseif ($request->filter === 'trending') {
                $query->where('is_trending', true);
            } elseif ($request->filter === 'in_stock') {
                $query->where('stock', '>', 0);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $products = $query->orderBy('id', 'desc')->paginate(15);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function createProduct()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $sellers = User::whereIn('role', ['seller', 'creator', 'admin'])->get();
        return view('admin.products.create', compact('categories', 'sellers'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'seller_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'images' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
        ]);

        $images = [];
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'prod_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $images[] = asset('uploads/products/' . $filename);
        } elseif (!empty($validated['images'])) {
            $images = array_map('trim', explode(',', $validated['images']));
        } else {
            $images[] = 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600';
        }

        $catName = null;
        if (!empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            $catName = $cat ? $cat->name : null;
        }

        Product::create([
            'seller_id' => $validated['seller_id'],
            'category_id' => $validated['category_id'] ?? null,
            'category' => $catName ?? 'General',
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'locked_stock' => 0,
            'images' => $images,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'is_trending' => $request->boolean('is_trending'),
            'is_natural_lighting_declared' => true,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $sellers = User::whereIn('role', ['seller', 'creator', 'admin'])->get();
        return view('admin.products.edit', compact('product', 'categories', 'sellers'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'seller_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
            'images' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = 'prod_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $product->images = [asset('uploads/products/' . $filename)];
        } elseif (!empty($validated['images'])) {
            $product->images = array_map('trim', explode(',', $validated['images']));
        }

        $catName = $product->category;
        if (!empty($validated['category_id'])) {
            $cat = Category::find($validated['category_id']);
            $catName = $cat ? $cat->name : $catName;
        }

        $product->seller_id = $validated['seller_id'];
        $product->category_id = $validated['category_id'] ?? null;
        $product->category = $catName;
        $product->title = $validated['title'];
        $product->description = $validated['description'] ?? $product->description;
        $product->price = $validated['price'];
        $product->compare_price = $validated['compare_price'] ?? null;
        $product->stock = $validated['stock'];
        $product->status = $validated['status'];
        $product->is_featured = $request->boolean('is_featured');
        $product->is_trending = $request->boolean('is_trending');
        $product->save();

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    public function toggleProductFeatured($id)
    {
        $product = Product::findOrFail($id);
        $product->is_featured = !$product->is_featured;
        $product->save();

        return back()->with('success', "Product featured status updated to " . ($product->is_featured ? 'Featured' : 'Standard') . ".");
    }

    public function toggleProductTrending($id)
    {
        $product = Product::findOrFail($id);
        $product->is_trending = !$product->is_trending;
        $product->save();

        return back()->with('success', "Product trending status updated to " . ($product->is_trending ? 'Trending' : 'Standard') . ".");
    }

    /**
     * Settings: General Settings Sub-menu.
     */
    public function generalSettings()
    {
        return view('admin.settings.general');
    }

    public function updateGeneralSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'copyright_text' => 'nullable|string|max:255',
            'logo_file' => 'nullable|image|max:3072',
            'favicon_file' => 'nullable|image|max:1024',
        ]);

        Setting::set('site_name', $request->site_name, 'general');
        Setting::set('site_tagline', $request->site_tagline, 'general');
        Setting::set('contact_email', $request->contact_email, 'general');
        Setting::set('contact_phone', $request->contact_phone, 'general');
        Setting::set('copyright_text', $request->copyright_text, 'general');

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            Setting::set('site_logo', 'uploads/settings/' . $filename, 'general');
        }

        if ($request->hasFile('favicon_file')) {
            $file = $request->file('favicon_file');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            Setting::set('site_favicon', 'uploads/settings/' . $filename, 'general');
        }

        return back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Settings: SEO Settings Sub-menu.
     */
    public function seoSettings()
    {
        return view('admin.settings.seo');
    }

    public function updateSeoSettings(Request $request)
    {
        $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'google_analytics_id' => 'nullable|string|max:100',
            'custom_header_scripts' => 'nullable|string',
            'og_image_file' => 'nullable|image|max:4096',
        ]);

        Setting::set('meta_title', $request->meta_title, 'seo');
        Setting::set('meta_description', $request->meta_description, 'seo');
        Setting::set('meta_keywords', $request->meta_keywords, 'seo');
        Setting::set('google_analytics_id', $request->google_analytics_id, 'seo');
        Setting::set('custom_header_scripts', $request->custom_header_scripts, 'seo');

        if ($request->hasFile('og_image_file')) {
            $file = $request->file('og_image_file');
            $filename = 'og_image_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/settings'), $filename);
            Setting::set('og_image', asset('uploads/settings/' . $filename), 'seo');
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    /**
     * Settings: System Settings Sub-menu.
     */
    public function systemSettings()
    {
        return view('admin.settings.system');
    }

    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'default_currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'timezone' => 'required|string|max:100',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'platform_commission_percent' => 'nullable|numeric|min:0|max:100',
            'creator_subscription_price' => 'nullable|numeric|min:0',
        ]);

        Setting::set('default_currency', $request->default_currency, 'system');
        Setting::set('currency_symbol', $request->currency_symbol, 'system');
        Setting::set('timezone', $request->timezone, 'system');
        Setting::set('date_format', $request->date_format, 'system');
        Setting::set('time_format', $request->time_format, 'system');
        Setting::set('platform_commission_percent', $request->platform_commission_percent ?? '7.0', 'system');
        Setting::set('creator_subscription_price', $request->creator_subscription_price ?? '7.99', 'system');

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Gifts Management (Full CRUD).
     */
     public function gifts(Request $request)
     {
         $query = Gift::query();

         if ($request->filled('search')) {
             $query->where('name', 'like', '%' . $request->search . '%')
                   ->orWhere('slug', 'like', '%' . $request->search . '%');
         }

         if ($request->filled('tier')) {
             $query->where('tier_ladder', $request->tier);
         }

         $gifts = $query->orderBy('coin_cost', 'asc')->paginate(12);

         return view('admin.gifts.index', compact('gifts'));
     }

    public function storeGift(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'coin_cost' => 'required|integer|min:1',
            'icon_url' => 'nullable|string',
            'icon_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:10240',
            'animation_type' => 'required|string|max:50',
            'animation_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm|max:20480',
            'tier_ladder' => 'required|string|in:standard,vip,exclusive',
            'is_active' => 'nullable|boolean',
        ]);

        $iconUrl = $request->icon_url;
        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $uploadDir = public_path('uploads/gifts');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'gift_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $iconUrl = '/uploads/gifts/' . $filename;
        }

        $animationType = $request->animation_type;
        if ($request->hasFile('animation_file')) {
            $animFile = $request->file('animation_file');
            $uploadDir = public_path('uploads/gifts');
            File::ensureDirectoryExists($uploadDir);
            $animFilename = 'anim_' . time() . '_' . Str::random(8) . '.' . $animFile->getClientOriginalExtension();
            $animFile->move($uploadDir, $animFilename);
            $animationType = '/uploads/gifts/' . $animFilename;
        }

        $slug = Str::slug($request->name);
        $count = Gift::where('slug', 'like', $slug . '%')->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }

        Gift::create([
            'name' => $request->name,
            'slug' => $slug,
            'coin_cost' => $request->coin_cost,
            'icon_url' => $iconUrl ?: 'assets/gifts/heart.svg',
            'animation_type' => $animationType ?: 'sparkle',
            'tier_ladder' => $request->tier_ladder,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Gift created successfully with uploaded media.');
    }

    public function updateGift(Request $request, $id)
    {
        $gift = Gift::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'coin_cost' => 'required|integer|min:1',
            'icon_url' => 'nullable|string',
            'icon_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:10240',
            'animation_type' => 'required|string|max:255',
            'animation_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg,mp4,webm|max:20480',
            'tier_ladder' => 'required|string|in:standard,vip,exclusive',
            'is_active' => 'nullable|boolean',
        ]);

        $iconUrl = $gift->icon_url;
        if ($request->hasFile('icon_file')) {
            $file = $request->file('icon_file');
            $uploadDir = public_path('uploads/gifts');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'gift_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $iconUrl = '/uploads/gifts/' . $filename;
        } elseif ($request->filled('icon_url')) {
            $iconUrl = $request->icon_url;
        }

        $animationType = $gift->animation_type;
        if ($request->hasFile('animation_file')) {
            $animFile = $request->file('animation_file');
            $uploadDir = public_path('uploads/gifts');
            File::ensureDirectoryExists($uploadDir);
            $animFilename = 'anim_' . time() . '_' . Str::random(8) . '.' . $animFile->getClientOriginalExtension();
            $animFile->move($uploadDir, $animFilename);
            $animationType = '/uploads/gifts/' . $animFilename;
        } elseif ($request->filled('animation_type')) {
            $animationType = $request->animation_type;
        }

        $gift->update([
            'name' => $request->name,
            'coin_cost' => $request->coin_cost,
            'icon_url' => $iconUrl,
            'animation_type' => $animationType,
            'tier_ladder' => $request->tier_ladder,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Gift updated successfully.');
    }

    public function deleteGift($id)
    {
        $gift = Gift::findOrFail($id);
        $gift->delete();

        return back()->with('success', 'Gift deleted successfully.');
    }

    public function toggleGift($id)
    {
        $gift = Gift::findOrFail($id);
        $gift->is_active = !$gift->is_active;
        $gift->save();

        return back()->with('success', 'Gift status updated.');
    }

    /**
     * Reactions & GIFs Management (Full CRUD).
     */
    public function reactions(Request $request)
    {
        $query = Reaction::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reactions = $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->paginate(16);

        return view('admin.reactions.index', compact('reactions'));
    }

    public function storeReaction(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:emoji,reaction,gif',
            'code' => 'nullable|string|max:50',
            'media_url' => 'nullable|string',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:20480',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $mediaUrl = $request->media_url;
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $uploadDir = public_path('uploads/reactions');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'react_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $mediaUrl = '/uploads/reactions/' . $filename;
        }

        Reaction::create([
            'name' => $request->name,
            'type' => $request->type,
            'code' => $request->code,
            'media_url' => $mediaUrl,
            'sort_order' => $request->integer('sort_order', 0),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Reaction / GIF created successfully with uploaded media.');
    }

    public function updateReaction(Request $request, $id)
    {
        $reaction = Reaction::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:emoji,reaction,gif',
            'code' => 'nullable|string|max:50',
            'media_url' => 'nullable|string',
            'media_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:20480',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $mediaUrl = $reaction->media_url;
        if ($request->hasFile('media_file')) {
            $file = $request->file('media_file');
            $uploadDir = public_path('uploads/reactions');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'react_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $mediaUrl = '/uploads/reactions/' . $filename;
        } elseif ($request->filled('media_url')) {
            $mediaUrl = $request->media_url;
        }

        $reaction->update([
            'name' => $request->name,
            'type' => $request->type,
            'code' => $request->code,
            'media_url' => $mediaUrl,
            'sort_order' => $request->integer('sort_order', 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Reaction / GIF updated successfully.');
    }

     public function deleteReaction($id)
     {
         $reaction = Reaction::findOrFail($id);
         $reaction->delete();

         return back()->with('success', 'Reaction / GIF deleted successfully.');
     }

     public function toggleReaction($id)
     {
         $reaction = Reaction::findOrFail($id);
         $reaction->is_active = !$reaction->is_active;
         $reaction->save();

         return back()->with('success', 'Reaction / GIF status updated.');
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

    /**
     * Auctions Management (Full CRUD)
     */
    public function auctions(Request $request)
    {
        $filter = $request->query('status', 'all');
        $search = $request->query('search', '');

        $query = Auction::with(['seller', 'product', 'highestBidder', 'bids']);

        if ($filter !== 'all' && in_array($filter, ['active', 'upcoming', 'sold', 'failed', 'cancelled'])) {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $auctions = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalAuctions = Auction::count();
        $activeAuctions = Auction::where('status', 'active')->count();
        $upcomingAuctions = Auction::where('status', 'upcoming')->count();
        $soldAuctions = Auction::where('status', 'sold')->count();
        $failedAuctions = Auction::where('status', 'failed')->count();

        return view('admin.auctions.index', compact(
            'auctions',
            'filter',
            'search',
            'totalAuctions',
            'activeAuctions',
            'upcomingAuctions',
            'soldAuctions',
            'failedAuctions'
        ));
    }

    public function createAuction()
    {
        $sellers = User::whereIn('role', ['seller', 'creator', 'admin'])->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('title')->get();

        return view('admin.auctions.create', compact('sellers', 'products'));
    }

    public function storeAuction(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'starting_bid' => 'required|numeric|min:0.01',
            'reserve_price' => 'nullable|numeric|min:0',
            'min_bid_step' => 'required|numeric|min:0.01',
            'seller_id' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'status' => 'required|in:active,upcoming,sold,failed,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $imageUrl = $validated['image_url'] ?? null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $uploadDir = public_path('uploads/auctions');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'auction_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $imageUrl = '/uploads/auctions/' . $filename;
        }

        // If product selected and no image provided, inherit product image
        if (!$imageUrl && !empty($validated['product_id'])) {
            $product = Product::find($validated['product_id']);
            if ($product && !empty($product->primary_image)) {
                $imageUrl = $product->primary_image;
            }
        }

        // Default placeholder image if none provided
        if (!$imageUrl) {
            $imageUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
        }

        $sellerId = $validated['seller_id'] ?: auth()->id();
        $startsAt = $validated['starts_at'] ? \Carbon\Carbon::parse($validated['starts_at']) : now();
        $endsAt = $validated['ends_at'] ? \Carbon\Carbon::parse($validated['ends_at']) : now()->addHours(24);

        $auction = Auction::create([
            'seller_id' => $sellerId,
            'product_id' => $validated['product_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $imageUrl,
            'starting_bid' => $validated['starting_bid'],
            'reserve_price' => $validated['reserve_price'] ?? null,
            'current_bid' => $validated['starting_bid'],
            'min_bid_step' => $validated['min_bid_step'] ?? 5.00,
            'status' => $validated['status'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_blurred' => false,
        ]);

        return redirect()->route('admin.auctions.index')->with('success', 'Auction "' . $auction->title . '" created successfully!');
    }

    public function editAuction($id)
    {
        $auction = Auction::with(['seller', 'product', 'bids'])->findOrFail($id);
        $sellers = User::whereIn('role', ['seller', 'creator', 'admin'])->orderBy('name')->get();
        $products = Product::where('status', 'active')->orderBy('title')->get();

        return view('admin.auctions.edit', compact('auction', 'sellers', 'products'));
    }

    public function updateAuction(Request $request, $id)
    {
        $auction = Auction::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:10240',
            'starting_bid' => 'required|numeric|min:0.01',
            'reserve_price' => 'nullable|numeric|min:0',
            'current_bid' => 'nullable|numeric|min:0.01',
            'min_bid_step' => 'required|numeric|min:0.01',
            'seller_id' => 'nullable|exists:users,id',
            'product_id' => 'nullable|exists:products,id',
            'status' => 'required|in:active,upcoming,sold,failed,cancelled',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'is_blurred' => 'nullable',
        ]);

        $imageUrl = $auction->image_url;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $uploadDir = public_path('uploads/auctions');
            File::ensureDirectoryExists($uploadDir);
            $filename = 'auction_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $imageUrl = '/uploads/auctions/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imageUrl = $request->image_url;
        }

        $startsAt = $validated['starts_at'] ? \Carbon\Carbon::parse($validated['starts_at']) : $auction->starts_at;
        $endsAt = $validated['ends_at'] ? \Carbon\Carbon::parse($validated['ends_at']) : $auction->ends_at;

        $auction->update([
            'seller_id' => $validated['seller_id'] ?: $auction->seller_id,
            'product_id' => $validated['product_id'] ?? $auction->product_id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_url' => $imageUrl,
            'starting_bid' => $validated['starting_bid'],
            'reserve_price' => $validated['reserve_price'] ?? null,
            'current_bid' => $validated['current_bid'] ?? $auction->current_bid,
            'min_bid_step' => $validated['min_bid_step'] ?? 5.00,
            'status' => $validated['status'],
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_blurred' => $request->boolean('is_blurred'),
        ]);

        return redirect()->route('admin.auctions.index')->with('success', 'Auction updated successfully!');
    }

    public function deleteAuction($id)
    {
        $auction = Auction::findOrFail($id);
        $title = $auction->title;
        $auction->delete();

        return redirect()->route('admin.auctions.index')->with('success', 'Auction "' . $title . '" deleted successfully.');
    }

    public function toggleAuctionStatus($id)
    {
        $auction = Auction::findOrFail($id);
        if ($auction->status === 'active') {
            $auction->status = 'cancelled';
        } else {
            $auction->status = 'active';
            if ($auction->ends_at && $auction->ends_at->isPast()) {
                $auction->ends_at = now()->addHours(24);
            }
        }
        $auction->save();

        return back()->with('success', 'Auction status changed to ' . ucfirst($auction->status) . '.');
    }

    public function toggleAuctionBlur($id)
    {
        $auction = Auction::findOrFail($id);
        $auction->is_blurred = !$auction->is_blurred;
        $auction->save();

        return back()->with('success', 'Auction blur state updated.');
    }
}
