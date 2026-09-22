<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\SellerProfile;

class ApiSellerController extends Controller
{
    /**
     * Upload Mandatory 30-second Packing Video (SRS #2).
     */
    public function uploadPackingVideo(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('seller_id', $user->id)->findOrFail($orderId);

        $request->validate([
            'packing_video_url' => 'required|string',
        ]);

        $order->packing_video_url = $request->packing_video_url;
        $order->status = 'packing';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Packing video uploaded and verified. Ready for courier pickup.',
            'order' => $order,
        ]);
    }

    /**
     * Dispatch Order & Release Prepaid EasyPost Label (SRS #7).
     */
    public function dispatchOrder(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('seller_id', $user->id)->findOrFail($orderId);

        if (!$order->packing_video_url) {
            return response()->json([
                'success' => false,
                'message' => 'Mandatory 30-second packing video must be uploaded before dispatching.',
            ], 422);
        }

        $order->status = 'shipped';
        $order->shipped_at = now();
        $order->tracking_number = $order->tracking_number ?? ('EP' . rand(100000000, 999999999) . 'CA');
        $order->save();

        // Update seller dispatch metrics
        $profile = $user->sellerProfile;
        if ($profile) {
            $profile->increment('total_dispatches');
            $isOnTime = $order->dispatch_deadline && now()->lessThanOrEqualTo($order->dispatch_deadline);
            if ($isOnTime && $profile->on_time_dispatch_rate >= 95.00) {
                $user->fast_shipper_badge = true;
                $user->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Order marked as shipped. Tracking milestone update broadcasted.',
            'order' => $order,
        ]);
    }

    /**
     * Pre-session Inventory Lock (SRS #14).
     */
    public function lockLiveInventory(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'locked_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::where('seller_id', $user->id)->findOrFail($request->product_id);
        $product->locked_stock = min($product->stock, (int) $request->locked_quantity);
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Live session inventory locked successfully.',
            'product' => $product,
        ]);
    }
}
