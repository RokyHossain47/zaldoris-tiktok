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

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'all');

        $products = Product::with('seller')
            ->where('status', 'active')
            ->when($category !== 'all', function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('shop.index', compact('products', 'category'));
    }

    public function product($id)
    {
        $product = Product::with('seller.sellerProfile')->findOrFail($id);
        $relatedProducts = Product::where('id', '!=', $id)
            ->where('category', $product->category)
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'relatedProducts'));
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

    public function success($orderId)
    {
        $order = Order::with(['items.product', 'seller.sellerProfile'])->findOrFail($orderId);

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
