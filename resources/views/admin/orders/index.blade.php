@extends('layouts.admin')

@section('title', 'Orders Management - Super Admin')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- HEADER -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Orders Management</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Manage customer orders, track shipping logistics, escrow payments, and product variant selections.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.products.index') }}" style="background: rgba(255, 255, 255, 0.05); color: #fff; text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="bi bi-box-seam"></i> Products Catalog
            </a>
            <a href="{{ route('admin.disputes') }}" style="background: rgba(254, 44, 85, 0.15); color: #FF5E85; text-decoration: none; padding: 10px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid rgba(254, 44, 85, 0.3);">
                <i class="bi bi-shield-check"></i> Escrow Disputes
            </a>
        </div>
    </div>

    <!-- STATS OVERVIEW -->
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Orders</div>
                <div class="stat-val">{{ $stats['total_orders'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(0, 240, 200, 0.1); color: var(--cyan-accent);">
                <i class="bi bi-bag-check-fill"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Pending / Packing</div>
                <div class="stat-val" style="color: #FFAA00;">{{ $stats['pending_orders'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(255, 170, 0, 0.1); color: #FFAA00;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">In Transit / Shipped</div>
                <div class="stat-val" style="color: #38BDF8;">{{ $stats['shipped_orders'] ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(56, 189, 248, 0.1); color: #38BDF8;">
                <i class="bi bi-truck"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Total Volume (Gross)</div>
                <div class="stat-val" style="color: var(--cyan-accent); font-size: 26px;">{{ setting('currency_symbol', 'C$') }}{{ number_format($stats['total_volume'] ?? 0, 2) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(0, 240, 200, 0.1); color: var(--cyan-accent);">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="admin-table-container">
        <!-- FILTER BAR -->
        <div class="table-header-bar">
            <div class="table-filter-pills">
                <a href="{{ route('admin.orders.index') }}" class="{{ !request('status') ? 'active' : '' }}">All Orders</a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="{{ request('status') === 'pending' ? 'active' : '' }}">⏳ Pending</a>
                <a href="{{ route('admin.orders.index', ['status' => 'packing']) }}" class="{{ request('status') === 'packing' ? 'active' : '' }}">📦 Packing</a>
                <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="{{ request('status') === 'shipped' ? 'active' : '' }}">🚚 Shipped</a>
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="{{ request('status') === 'delivered' ? 'active' : '' }}">✅ Delivered</a>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="{{ request('status') === 'cancelled' ? 'active' : '' }}">❌ Cancelled</a>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 10px; align-items: center;">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div style="position: relative;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, Buyer, Email, Tracking..." style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 14px 8px 34px; border-radius: 8px; font-size: 13px; width: 280px;">
                    <i class="bi bi-search" style="position: absolute; left: 12px; top: 9px; color: var(--text-muted); font-size: 12px;"></i>
                </div>
                <button type="submit" style="background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    Search
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}" style="color: var(--text-muted); font-size: 12px; text-decoration: none;">Reset</a>
                @endif
            </form>
        </div>

        <!-- ORDERS DATA TABLE -->
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Order & Date</th>
                    <th>Customer / Buyer</th>
                    <th>Items & Variants</th>
                    <th>Total & Escrow</th>
                    <th>Order Status</th>
                    <th>Carrier / Tracking</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusClass = match($order->status) {
                            'delivered', 'completed' => 'status-active',
                            'cancelled', 'disputed' => 'status-blocked',
                            'shipped' => 'status-shipped',
                            default => 'status-pending',
                        };

                        $paymentClass = match($order->payment_status) {
                            'released_to_seller' => 'status-active',
                            'escrow_held' => 'status-escrow',
                            'refunded' => 'status-blocked',
                            default => 'status-pending',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 800; color: #fff; font-size: 14px; font-family: monospace;">{{ $order->order_number }}</div>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                <i class="bi bi-calendar3"></i> {{ $order->created_at->format('M d, Y H:i') }}
                            </div>
                        </td>

                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="{{ $order->buyer && $order->buyer->avatar_url ? $order->buyer->avatar_url : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=60' }}" alt="Buyer" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <div style="font-weight: 700; color: #fff;">{{ $order->customer_name ?: ($order->buyer ? $order->buyer->name : 'Guest Customer') }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">{{ $order->customer_email ?: ($order->buyer ? $order->buyer->email : 'N/A') }}</div>
                                </div>
                            </div>
                        </td>

                        <td style="max-width: 240px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                @foreach($order->items as $item)
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <img src="{{ $item->product_image ?: ($item->product ? $item->product->primary_image : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=60') }}" alt="Item" style="width: 28px; height: 28px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-color);">
                                        <div style="font-size: 12px; color: #E2E8F0; line-height: 1.3;">
                                            <strong>{{ $item->quantity }}x</strong> {{ Str::limit($item->product_title, 22) }}
                                            @if($item->selected_color || $item->selected_size)
                                                <div style="font-size: 10px; color: var(--cyan-accent);">
                                                    {{ $item->selected_color ? $item->selected_color : '' }} 
                                                    {{ $item->selected_size ? '• ' . $item->selected_size : '' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>

                        <td>
                            <div style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 16px; color: var(--cyan-accent);">
                                {{ setting('currency_symbol', 'C$') }}{{ number_format($order->total_amount, 2) }}
                            </div>
                            <span class="status-badge {{ $paymentClass }}" style="font-size: 10px; padding: 2px 6px; margin-top: 4px;">
                                {{ str_replace('_', ' ', strtoupper($order->payment_status)) }}
                            </span>
                        </td>

                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>

                        <td>
                            @if($order->tracking_number)
                                <div style="font-size: 12px; font-weight: 700; color: #fff; font-family: monospace;">{{ $order->tracking_number }}</div>
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase;">{{ $order->carrier ?: 'EasyPost' }}</div>
                            @else
                                <span style="font-size: 12px; color: var(--text-muted);">Unassigned</span>
                            @endif
                        </td>

                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <button type="button" class="btn-action-icon" title="View & Edit Order" onclick="openOrderModal({{ $order->id }})" style="background: rgba(0, 240, 200, 0.1); color: var(--cyan-accent); border: 1px solid rgba(0, 240, 200, 0.25); width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                
                                <form action="{{ route('admin.orders.delete', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete order {{ $order->order_number }}?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon" title="Delete Order" style="background: rgba(254, 44, 85, 0.1); color: var(--pink-accent); border: 1px solid rgba(254, 44, 85, 0.25); width: 32px; height: 32px; border-radius: 8px; cursor: pointer;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                            <i class="bi bi-inbox" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                            No orders found matching the filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- EDIT / MANAGE ORDER MODAL -->
<div id="orderManageModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.8); position: relative;">
        <button type="button" onclick="closeOrderModal()" style="position: absolute; right: 20px; top: 20px; background: transparent; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">
            <i class="bi bi-x-lg"></i>
        </button>

        <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 4px;" id="modalOrderTitle">Manage Order #ORD-XXXX</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Update order fulfillment status, carrier details, and escrow payment lifecycle.</p>

        <form id="orderStatusForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Fulfillment Status *</label>
                    <select name="status" id="modalOrderStatus" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                        <option value="pending">Pending (Awaiting Processing)</option>
                        <option value="packing">Packing (Seller Preparing Package)</option>
                        <option value="shipped">Shipped (In Transit)</option>
                        <option value="delivered">Delivered (Completed)</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="disputed">Disputed / Under Review</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Payment / Escrow Status *</label>
                    <select name="payment_status" id="modalPaymentStatus" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                        <option value="pending">Pending</option>
                        <option value="escrow_held">Escrow Held (Secured by Zaldoris)</option>
                        <option value="released_to_seller">Released to Seller</option>
                        <option value="refunded">Refunded to Buyer</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Carrier</label>
                    <input type="text" name="carrier" id="modalOrderCarrier" placeholder="e.g. Canada Post / EasyPost / FedEx" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px;">Tracking Number</label>
                    <input type="text" name="tracking_number" id="modalOrderTracking" placeholder="e.g. EP8921892189CA" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                </div>
            </div>

            <!-- SHIPPING ADDRESS & DETAILS PREVIEW -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 700; color: var(--cyan-accent); text-transform: uppercase; margin-bottom: 6px;">Shipping Destination</div>
                <div id="modalShippingAddress" style="font-size: 13px; color: #CBD5E1; line-height: 1.5;">Loading address...</div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeOrderModal()" style="background: rgba(255, 255, 255, 0.08); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), #00D8B4); color: #090D10; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .status-pending { background: rgba(255, 170, 0, 0.15); color: #FFAA00; }
    .status-shipped { background: rgba(56, 189, 248, 0.15); color: #38BDF8; }
    .status-escrow { background: rgba(112, 51, 255, 0.2); color: #A78BFA; }
</style>

<script>
    const orderDataMap = {
        @foreach($orders as $o)
            {{ $o->id }}: {
                number: @json($o->order_number),
                status: @json($o->status),
                payment_status: @json($o->payment_status),
                carrier: @json($o->carrier ?? 'EasyPost'),
                tracking_number: @json($o->tracking_number ?? ''),
                shipping_address: @json($o->shipping_address ?? []),
                customer_name: @json($o->customer_name ?? ($o->buyer ? $o->buyer->name : 'N/A')),
                update_url: "{{ route('admin.orders.update_status', $o->id) }}"
            },
        @endforeach
    };

    function openOrderModal(orderId) {
        const data = orderDataMap[orderId];
        if (!data) return;

        document.getElementById('modalOrderTitle').textContent = `Manage Order #${data.number}`;
        document.getElementById('modalOrderStatus').value = data.status;
        document.getElementById('modalPaymentStatus').value = data.payment_status;
        document.getElementById('modalOrderCarrier').value = data.carrier;
        document.getElementById('modalOrderTracking').value = data.tracking_number;
        document.getElementById('orderStatusForm').action = data.update_url;

        let addrStr = data.customer_name + '<br>';
        if (data.shipping_address && typeof data.shipping_address === 'object') {
            addrStr += (data.shipping_address.street || '') + ', ' +
                       (data.shipping_address.city || '') + ', ' +
                       (data.shipping_address.province || '') + ' ' +
                       (data.shipping_address.postal_code || '') + '<br>' +
                       (data.shipping_address.country || '');
        } else {
            addrStr += 'Standard Address on File';
        }
        document.getElementById('modalShippingAddress').innerHTML = addrStr;

        const modal = document.getElementById('orderManageModal');
        modal.style.display = 'flex';
    }

    function closeOrderModal() {
        document.getElementById('orderManageModal').style.display = 'none';
    }
</script>
@endsection
