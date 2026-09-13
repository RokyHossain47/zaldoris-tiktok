<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\TaxService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category', 'all');
        $filter = $request->query('filter');

        $categories = Category::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $query = Product::with(['seller', 'category'])->where('status', 'active');

        if ($categorySlug !== 'all') {
            $cat = Category::where('slug', $categorySlug)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            } else {
                $query->where('category', 'like', "%{$categorySlug}%");
            }
        }

        if ($filter === 'featured') {
            $query->where('is_featured', true);
        } elseif ($filter === 'trending') {
            $query->where('is_trending', true);
        }

        $products = $query->orderBy('id', 'desc')->paginate(12);

        return view('shop.index', compact('products', 'categories', 'categorySlug', 'filter'));
    }

    public function product($id = null)
    {
        $product = $id ? Product::with(['seller.sellerProfile', 'category'])->find($id) : null;
        if (!$product) {
            $product = Product::with(['seller.sellerProfile', 'category'])->first() ?? new Product();
        }

        // Store recently viewed product in browser session (up to 10 latest unique items)
        if ($product->id) {
            $recent = session()->get('recently_viewed_products', []);
            $recent = array_values(array_diff($recent, [$product->id]));
            array_unshift($recent, $product->id);
            $recent = array_slice($recent, 0, 10);
            session()->put('recently_viewed_products', $recent);
        }

        $relatedProducts = Product::where('id', '!=', $product->id ?? 0)
            ->where('status', 'active')
            ->take(4)
            ->get();

        $stream = ($product->id ? $product->streams()->where('is_live', true)->first() : null)
            ?? \App\Models\Stream::with('host')->where('is_live', true)->first() 
            ?? \App\Models\Stream::with('host')->first();

        $reactions = \App\Models\Reaction::where('is_active', true)
            ->where('type', 'emoji')
            ->orderBy('sort_order', 'asc')
            ->get();

        $gifs = \App\Models\Reaction::where('is_active', true)
            ->where('type', 'gif')
            ->orderBy('sort_order', 'asc')
            ->get();

        $gifts = \App\Models\Gift::where('is_active', true)
            ->orderBy('coin_cost', 'asc')
            ->get();

        return view('shop.product', compact('product', 'relatedProducts', 'stream', 'reactions', 'gifs', 'gifts'));
    }

    public function cart()
    {
        return view('shop.cart');
    }

    public function checkout()
    {
        return view('shop.checkout');
    }

    public function payment()
    {
        return view('shop.payment');
    }

    public function processCheckout(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'street' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'payment_method' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $user) {
            $subtotal = 0.00;
            $itemsToCreate = [];
            $primarySellerId = null;

            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if ($product->available_stock < $item['quantity']) {
                    return back()->withErrors(['error' => "Product '{$product->title}' has insufficient stock."]);
                }

                $product->stock -= $item['quantity'];
                $product->save();

                $primarySellerId = $product->seller_id;
                $lineTotal = round($product->price * $item['quantity'], 2);
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'total_price' => $lineTotal,
                ];
            }

            $totals = TaxService::calculateOrderTotals($subtotal);
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            $order = Order::create([
                'order_number' => $orderNumber,
                'buyer_id' => $user ? $user->id : null,
                'seller_id' => $primarySellerId,
                'subtotal' => $totals['subtotal'],
                'shipping_fee' => $totals['shipping_fee'],
                'insurance_fee' => $totals['insurance_fee'],
                'hst_tax' => $totals['hst_tax'],
                'platform_commission' => $totals['platform_commission'],
                'total_amount' => $totals['total_amount'],
                'payment_status' => 'escrow_held',
                'payment_method' => $validated['payment_method'] ?? 'stripe_card',
                'customer_email' => $user ? $user->email : 'buyer@example.com',
                'customer_name' => $user ? $user->name : 'Valued Customer',
                'shipping_address' => [
                    'street' => $validated['street'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'country' => $validated['country'],
                ],
                'status' => 'packing',
                'dispatch_deadline' => now()->addHours(48),
                'tracking_number' => 'EP' . rand(100000000, 999999999) . 'CA',
                'carrier' => 'easypost',
                'requires_signature' => $totals['requires_signature'],
            ]);

            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            return redirect()->route('shop.success', ['order' => $order->id]);
        });
    }

    public function success($orderId = null)
    {
        $order = $orderId ? Order::with(['items.product', 'seller.sellerProfile'])->find($orderId) : null;
        if (!$order) {
            $order = Order::with(['items.product', 'seller.sellerProfile'])->latest()->first() ?? new Order();
        }

        return view('shop.success', compact('order'));
    }

    public function orders()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $orders = Order::with(['items.product', 'seller'])
            ->where('buyer_id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('shop.orders', compact('orders'));
    }
}
