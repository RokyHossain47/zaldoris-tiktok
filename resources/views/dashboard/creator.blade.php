@extends('layouts.app')

@section('title', 'User Hub & Account Studio - ' . setting('site_name', 'Zaldoris'))

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 10px 16px 60px;">

    <!-- TOP PROFILE BANNER & QUICK STATS -->
    <div class="zal-card" style="background: linear-gradient(135deg, #181D26, #11161E); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 24px 28px; margin-bottom: 24px; box-shadow: 0 6px 24px rgba(0,0,0,0.35);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 18px;">
                <div style="position: relative;">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid #00F0C8;">
                    <span style="position: absolute; bottom: 0; right: 0; background: #00F0C8; color: #000; font-size: 11px; font-weight: 900; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #11161E;">✓</span>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin: 0;">{{ $user->name }}</h1>
                        <span style="background: rgba(0, 240, 200, 0.12); color: #00F0C8; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 12px; border: 1px solid rgba(0, 240, 200, 0.3);">
                            {{ strtoupper($user->role ?? 'BUYER') }}
                        </span>
                    </div>
                    <div style="color: #8E9AA8; font-size: 13px; margin-top: 5px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                        <span><i class="bi bi-envelope" style="margin-right: 4px;"></i> {{ $user->email }}</span>
                        @if($user->phone)
                            <span><i class="bi bi-telephone" style="margin-right: 4px;"></i> {{ $user->phone }}</span>
                        @endif
                        <span><i class="bi bi-people" style="margin-right: 4px;"></i> {{ $following->count() }} Following</span>
                    </div>
                </div>
            </div>

            <!-- Quick Wallet / Net Payout Box -->
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 12px 20px; border-radius: 14px; text-align: center;">
                    <div style="font-size: 11px; color: #8E9AA8; text-transform: uppercase; font-weight: 700;">Coin Balance</div>
                    <div style="font-size: 20px; font-weight: 900; color: #FFB800;">🪙 {{ number_format($user->coin_balance ?? 0) }}</div>
                </div>
                <div style="background: rgba(0, 240, 200, 0.06); border: 1px solid rgba(0, 240, 200, 0.2); padding: 12px 20px; border-radius: 14px; text-align: center;">
                    <div style="font-size: 11px; color: #00F0C8; text-transform: uppercase; font-weight: 700;">Wallet / Earnings</div>
                    <div style="font-size: 20px; font-weight: 900; color: #00F0C8;">${{ number_format($user->earnings_usd ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFICATIONS / ALERTS -->
    @if(session('success'))
        <div style="background: rgba(0, 240, 200, 0.12); border: 1px solid #00F0C8; color: #00F0C8; padding: 12px 18px; border-radius: 12px; font-size: 13px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.12); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 18px; border-radius: 12px; font-size: 13px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MAIN USER PANEL NAVIGATION TABS -->
    @php
        $activeTab = request('tab', 'orders');
    @endphp
    <div style="display: flex; gap: 10px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px; overflow-x: auto; scrollbar-width: none;">
        <button class="user-tab-btn {{ $activeTab === 'orders' ? 'active' : '' }}" onclick="switchUserTab('orders')">
            <i class="bi bi-bag-check"></i> My Orders ({{ $buyerOrders->count() }})
        </button>
        <button class="user-tab-btn {{ $activeTab === 'addresses' ? 'active' : '' }}" onclick="switchUserTab('addresses')">
            <i class="bi bi-geo-alt"></i> Shipping Addresses ({{ $addresses->count() }})
        </button>
        <button class="user-tab-btn {{ $activeTab === 'profile' ? 'active' : '' }}" onclick="switchUserTab('profile')">
            <i class="bi bi-person-gear"></i> Edit Profile & Security
        </button>
        <button class="user-tab-btn {{ $activeTab === 'following' ? 'active' : '' }}" onclick="switchUserTab('following')">
            <i class="bi bi-person-plus"></i> Following Creators ({{ $following->count() }})
        </button>
        @if($user->isCreator() || $user->isAdmin())
            <button class="user-tab-btn {{ $activeTab === 'creator' ? 'active' : '' }}" onclick="switchUserTab('creator')">
                <i class="bi bi-broadcast-pin"></i> Creator Studio
            </button>
        @endif
    </div>

    <!-- TAB 1: MY ORDERS -->
    <div id="tabContent_orders" class="user-tab-pane" style="{{ $activeTab === 'orders' ? 'display: block;' : 'display: none;' }}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0;">Current & Past Orders</h2>
            <a href="{{ route('shop.index') }}" class="btn-cyan-outline" style="text-decoration: none; padding: 6px 14px; font-size: 12px; border-radius: 8px; font-weight: 700; color: #00F0C8; border: 1px solid #00F0C8;">
                <i class="bi bi-plus-lg"></i> Shop More Items
            </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 16px;">
            @forelse($buyerOrders as $order)
                <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 14px; margin-bottom: 14px;">
                        <div>
                            <div style="font-size: 15px; font-weight: 800; color: #fff;">
                                Order #{{ $order->order_number }}
                            </div>
                            <small style="color: #8E9AA8; font-size: 12px;">Placed on {{ $order->created_at->format('M d, Y • h:i A') }}</small>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            @php
                                $statusColor = match($order->status) {
                                    'delivered' => '#00F0C8',
                                    'shipped' => '#38BDF8',
                                    'packing' => '#FBBF24',
                                    'cancelled' => '#FE2C55',
                                    default => '#94A3B8'
                                };
                            @endphp
                            <span style="background: rgba(255,255,255,0.05); color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 8px; text-transform: uppercase;">
                                ● {{ $order->status }}
                            </span>
                            <span style="font-size: 16px; font-weight: 900; color: #00F0C8;">
                                ${{ number_format($order->total_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @foreach($order->items as $item)
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 14px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $item->product_image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100' }}" alt="{{ $item->product_title }}" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #1a222d;">
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: #fff;">{{ $item->product_title }}</div>
                                        <div style="font-size: 11px; color: #8E9AA8;">
                                            @if($item->selected_color) Color: {{ $item->selected_color }} • @endif
                                            @if($item->selected_size) Size: {{ $item->selected_size }} • @endif
                                            Qty: {{ $item->quantity }}
                                        </div>
                                    </div>
                                </div>
                                <div style="font-size: 13px; font-weight: 700; color: #fff;">
                                    ${{ number_format($item->total_price, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Delivery & Tracking Info Footer -->
                    <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; font-size: 12px; color: #8E9AA8;">
                        <div>
                            <i class="bi bi-truck" style="color: #00F0C8;"></i> Carrier: <strong>{{ $order->carrier ?? 'Standard Ground Delivery' }}</strong> 
                            @if($order->tracking_number)
                                • Tracking: <span style="color: #fff; font-family: monospace;">{{ $order->tracking_number }}</span>
                            @endif
                        </div>
                        <div style="color: #fff;">
                            Payment: <span style="text-transform: capitalize; color: #00F0C8; font-weight: 700;">{{ str_replace('_', ' ', $order->payment_method ?? 'Stripe') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 40px; text-align: center; color: #8E9AA8;">
                    <i class="bi bi-cart-x" style="font-size: 40px; color: #00F0C8; display: block; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; font-size: 16px; margin-bottom: 6px;">No Orders Placed Yet</h3>
                    <p style="font-size: 13px; margin-bottom: 16px;">Browse trending products, watch live shopping streams, and make your first purchase.</p>
                    <a href="{{ route('shop.index') }}" style="background: #00F0C8; color: #000; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 13px;">Explore Live Shop</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 2: SHIPPING ADDRESSES -->
    <div id="tabContent_addresses" class="user-tab-pane" style="{{ $activeTab === 'addresses' ? 'display: block;' : 'display: none;' }}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div>
                <h2 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0;">Delivery Addresses</h2>
                <small style="color: #8E9AA8; font-size: 12px;">Manage delivery destinations and set your default checkout address.</small>
            </div>
            <button type="button" class="btn-cyan-solid" onclick="openAddAddressModal()">
                <i class="bi bi-plus-lg"></i> Add New Address
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
            @forelse($addresses as $addr)
                <div class="zal-card" style="background: #11161E; border: 1px solid {{ $addr->is_default ? '#00F0C8' : 'rgba(255,255,255,0.08)' }}; border-radius: 16px; padding: 20px; position: relative;">
                    @if($addr->is_default)
                        <span style="position: absolute; top: 16px; right: 16px; background: rgba(0,240,200,0.15); color: #00F0C8; border: 1px solid #00F0C8; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                            ★ Default
                        </span>
                    @endif

                    <div style="font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 4px;">
                        {{ $addr->recipient_name }}
                    </div>
                    <div style="font-size: 12px; color: #8E9AA8; margin-bottom: 10px;">
                        <i class="bi bi-telephone"></i> {{ $addr->phone }}
                    </div>

                    <div style="font-size: 13px; color: #CBD5E1; line-height: 1.4; margin-bottom: 16px; min-height: 40px;">
                        {{ $addr->address_line1 }}@if($addr->address_line2), {{ $addr->address_line2 }}@endif<br>
                        {{ $addr->city }}, {{ $addr->region }} {{ $addr->postal_code }}, {{ $addr->country }}
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 12px;">
                        @if(!$addr->is_default)
                            <form action="{{ route('user.address.set_default', $addr->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: #00F0C8; font-size: 12px; font-weight: 700; cursor: pointer; padding: 0;">
                                    Set as Default
                                </button>
                            </form>
                        @else
                            <span style="font-size: 12px; color: #00F0C8; font-weight: 600;"><i class="bi bi-check-lg"></i> Default Address</span>
                        @endif

                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-icon-action" onclick='openEditAddressModal(@json($addr))' title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('user.address.delete', $addr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action" style="color: #FE2C55;" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="zal-card" style="grid-column: 1 / -1; background: #11161E; border: 1px dashed rgba(255,255,255,0.15); border-radius: 16px; padding: 40px; text-align: center; color: #8E9AA8;">
                    <i class="bi bi-geo-alt" style="font-size: 40px; color: #00F0C8; display: block; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; font-size: 16px; margin-bottom: 6px;">No Shipping Addresses Saved</h3>
                    <p style="font-size: 13px; margin-bottom: 16px;">Add a delivery address to make checking out seamless and fast.</p>
                    <button type="button" class="btn-cyan-solid" onclick="openAddAddressModal()">+ Add Your First Address</button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 3: EDIT PROFILE & SECURITY -->
    <div id="tabContent_profile" class="user-tab-pane" style="{{ $activeTab === 'profile' ? 'display: block;' : 'display: none;' }}">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            
            <!-- Personal Info Card -->
            <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-person-badge" style="color: #00F0C8;"></i> Profile Information
                </h3>

                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <img src="{{ $user->avatar_url }}" id="avatarPreview" alt="Avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid #00F0C8;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 4px;">Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*" onchange="previewAvatar(event)" style="font-size: 12px; color: #CBD5E1;">
                        </div>
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input-dark">
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled class="form-input-dark" style="opacity: 0.6; cursor: not-allowed;">
                        <small style="font-size: 11px; color: #64748B;">Contact support to change your account email.</small>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000" class="form-input-dark">
                    </div>

                    <button type="submit" class="btn-cyan-solid" style="width: 100%;">Save Profile Changes</button>
                </form>
            </div>

            <!-- Password & Security Card -->
            <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-shield-lock" style="color: #00F0C8;"></i> Security & Password
                </h3>

                <form action="{{ route('user.password.update') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">Current Password *</label>
                        <input type="password" name="current_password" required placeholder="••••••••" class="form-input-dark">
                    </div>

                    <div style="margin-bottom: 14px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">New Password *</label>
                        <input type="password" name="password" required placeholder="Minimum 6 characters" class="form-input-dark">
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #8E9AA8; margin-bottom: 6px;">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" required placeholder="Repeat new password" class="form-input-dark">
                    </div>

                    <button type="submit" class="btn-cyan-solid" style="width: 100%;">Update Password</button>
                </form>
            </div>

        </div>
    </div>

    <!-- TAB 4: FOLLOWING CREATORS -->
    <div id="tabContent_following" class="user-tab-pane" style="{{ $activeTab === 'following' ? 'display: block;' : 'display: none;' }}">
        <div style="margin-bottom: 18px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0;">Following ({{ $following->count() }})</h2>
            <small style="color: #8E9AA8; font-size: 12px;">Discover and manage live streams and auctions from your favorite creators.</small>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            @forelse($following as $creator)
                <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 18px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="{{ $creator->avatar_url }}" alt="{{ $creator->name }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #00F0C8;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #fff;">{{ $creator->name }}</div>
                            <div style="font-size: 11px; color: #8E9AA8;">{{ number_format($creator->followers()->count()) }} Followers</div>
                        </div>
                    </div>
                    <button type="button" class="btn-unfollow" id="followBtn_{{ $creator->id }}" onclick="toggleFollowCreator({{ $creator->id }})">
                        Following
                    </button>
                </div>
            @empty
                <div class="zal-card" style="grid-column: 1 / -1; background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 40px; text-align: center; color: #8E9AA8;">
                    <i class="bi bi-person-heart" style="font-size: 40px; color: #FE2C55; display: block; margin-bottom: 10px;"></i>
                    <h3 style="color: #fff; font-size: 16px; margin-bottom: 6px;">You Aren't Following Any Creators Yet</h3>
                    <p style="font-size: 13px; margin-bottom: 16px;">Follow top live streamers to get notified when they go live with exclusive drops.</p>
                    <a href="{{ route('streams.index') }}" style="background: #00F0C8; color: #000; text-decoration: none; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 13px;">Discover Live Streamers</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TAB 5: CREATOR STUDIO (SRS Page 4 & 5) -->
    @if($user->isCreator() || $user->isAdmin())
        <div id="tabContent_creator" class="user-tab-pane" style="{{ $activeTab === 'creator' ? 'display: block;' : 'display: none;' }}">
            <!-- REVENUE DEDUCTION BREAKDOWN -->
            <div style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; font-size: 13px; margin-bottom: 24px;">
                <div>
                    <div style="color: #8E9AA8; margin-bottom: 4px;">Gross Gift Volume</div>
                    <div style="font-size: 18px; font-weight: 800; color: #fff;">${{ number_format($grossGiftsUsd, 2) }}</div>
                </div>
                <div>
                    <div style="color: #8E9AA8; margin-bottom: 4px;">App Store Fees (~15%)</div>
                    <div style="font-size: 18px; font-weight: 800; color: #FE2C55;">-${{ number_format($appStoreFees, 2) }}</div>
                </div>
                <div>
                    <div style="color: #8E9AA8; margin-bottom: 4px;">13% HST Tax</div>
                    <div style="font-size: 18px; font-weight: 800; color: #FE2C55;">-${{ number_format($hstTaxes, 2) }}</div>
                </div>
                <div>
                    <div style="color: #8E9AA8; margin-bottom: 4px;">60% Creator Cut</div>
                    <div style="font-size: 18px; font-weight: 800; color: #00F0C8;">+${{ number_format($creatorNetPayout, 2) }}</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- RECENT GIFTS RECEIVED -->
                <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px;">
                    <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 16px;">Recent Live Gifts Received</h3>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($giftsReceived as $giftTx)
                            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 10px 14px; border-radius: 10px; font-size: 13px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 20px;">🎁</span>
                                    <div>
                                        <div style="font-weight: 700; color: #fff;">{{ $giftTx->gift->name ?? 'Gift' }} from {{ $giftTx->sender->name ?? 'Fan' }}</div>
                                        <div style="font-size: 11px; color: #8E9AA8;">{{ $giftTx->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 800; color: #00F0C8;">+${{ number_format($giftTx->creator_earning_usd, 2) }}</div>
                                    <div style="font-size: 11px; color: #FFB800;">{{ $giftTx->coin_amount }} coins</div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #8E9AA8; padding: 30px;">No live gifts received yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- SUBSCRIBERS ($7.99/mo) -->
                <div class="zal-card" style="background: #11161E; border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 16px; font-weight: 800; color: #fff; margin: 0;">Active Subscribers ($7.99/mo)</h3>
                        <span style="background: rgba(254,44,85,0.15); color: #FE2C55; font-size: 12px; font-weight: 800; padding: 2px 10px; border-radius: 12px;">
                            {{ $subscribers->count() }} VIPs
                        </span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($subscribers as $sub)
                            <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); padding: 10px 14px; border-radius: 10px; font-size: 13px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="{{ $sub->subscriber->avatar_url }}" alt="Subscriber" style="width: 32px; height: 32px; border-radius: 50%;">
                                    <div>
                                        <div style="font-weight: 700; color: #fff;">{{ $sub->subscriber->name }}</div>
                                        <div style="font-size: 11px; color: #8E9AA8;">Active Subscriber</div>
                                    </div>
                                </div>
                                <div style="font-weight: 800; color: #00F0C8;">$7.99/mo</div>
                            </div>
                        @empty
                            <div style="text-align: center; color: #8E9AA8; padding: 30px;">No active subscribers yet. Share your profile link!</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- ADD / EDIT ADDRESS MODAL -->
<div class="modal fade" id="userAddressModal" tabindex="-1" aria-labelledby="userAddressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #11161E; border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 16px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
                <h5 class="modal-title" id="userAddressModalTitle" style="font-weight: 800; font-size: 16px;">
                    <i class="bi bi-geo-alt-fill" style="color: #00F0C8;"></i> Add Shipping Address
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm" action="{{ route('user.address.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="addressFormMethod" value="POST">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Recipient Full Name *</label>
                        <input type="text" name="recipient_name" id="modal_recipient_name" class="form-input-dark" required placeholder="e.g. Alex Velocity" value="{{ $user->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Contact Phone Number *</label>
                        <input type="text" name="phone" id="modal_phone" class="form-input-dark" required placeholder="e.g. +1 (604) 555-0192" value="{{ $user->phone }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Street Address Line 1 *</label>
                        <input type="text" name="address_line1" id="modal_address_line1" class="form-input-dark" required placeholder="e.g. 100 King Street West">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Apt / Suite / Unit (Optional)</label>
                        <input type="text" name="address_line2" id="modal_address_line2" class="form-input-dark" placeholder="e.g. Suite 400">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">City *</label>
                            <input type="text" name="city" id="modal_city" class="form-input-dark" required placeholder="e.g. Toronto">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Province / State *</label>
                            <input type="text" name="region" id="modal_region" class="form-input-dark" required placeholder="e.g. ON">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Postal / ZIP Code *</label>
                            <input type="text" name="postal_code" id="modal_postal_code" class="form-input-dark" required placeholder="e.g. M5X 1A9">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size: 12px; color: #8E9AA8; font-weight: 600;">Country *</label>
                            <input type="text" name="country" id="modal_country" class="form-input-dark" required value="Canada">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="modal_is_default" style="accent-color: #00F0C8;">
                        <label class="form-check-label" for="modal_is_default" style="font-size: 13px; color: #CBD5E1;">
                            Set as default shipping address
                        </label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.08);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.08); border: none; font-size: 13px;">Cancel</button>
                    <button type="submit" class="btn-cyan-solid">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .user-tab-btn {
        background: transparent;
        border: none;
        color: #8E9AA8;
        font-size: 14px;
        font-weight: 700;
        padding: 8px 18px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .user-tab-btn:hover {
        color: #fff;
        background: rgba(255,255,255,0.05);
    }
    .user-tab-btn.active {
        background: #00F0C8;
        color: #000;
    }
    .btn-cyan-solid {
        background: #00F0C8;
        color: #000;
        border: none;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-cyan-solid:hover {
        background: #1ed6d0;
        transform: translateY(-1px);
        color: #000;
    }
    .form-input-dark {
        width: 100%;
        background: #18202A;
        border: 1px solid rgba(255,255,255,0.1);
        color: #fff;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        outline: none;
        transition: border 0.2s ease;
    }
    .form-input-dark:focus {
        border-color: #00F0C8;
    }
    .btn-icon-action {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.08);
        color: #CBD5E1;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-icon-action:hover {
        background: rgba(0, 240, 200, 0.15);
        color: #00F0C8;
        border-color: #00F0C8;
    }
    .btn-unfollow {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-unfollow:hover {
        background: rgba(254, 44, 85, 0.15);
        color: #FE2C55;
        border-color: #FE2C55;
    }
    .btn-unfollow.not-following {
        background: #00F0C8;
        color: #000;
        border-color: #00F0C8;
    }
</style>

<script>
    function switchUserTab(tabName) {
        document.querySelectorAll('.user-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.user-tab-pane').forEach(pane => pane.style.display = 'none');
        
        event.currentTarget.classList.add('active');
        const target = document.getElementById('tabContent_' + tabName);
        if (target) target.style.display = 'block';

        // Update URL query parameter without full reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
    }

    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openAddAddressModal() {
        document.getElementById('userAddressModalTitle').innerHTML = '<i class="bi bi-geo-alt-fill" style="color: #00F0C8;"></i> Add Shipping Address';
        document.getElementById('addressForm').action = "{{ route('user.address.store') }}";
        document.getElementById('addressFormMethod').value = "POST";
        document.getElementById('modal_address_line1').value = '';
        document.getElementById('modal_address_line2').value = '';
        document.getElementById('modal_city').value = '';
        document.getElementById('modal_region').value = '';
        document.getElementById('modal_postal_code').value = '';
        document.getElementById('modal_is_default').checked = false;

        const modal = new bootstrap.Modal(document.getElementById('userAddressModal'));
        modal.show();
    }

    function openEditAddressModal(addr) {
        document.getElementById('userAddressModalTitle').innerHTML = '<i class="bi bi-pencil-square" style="color: #00F0C8;"></i> Edit Shipping Address';
        document.getElementById('addressForm').action = `/address/${addr.id}`;
        document.getElementById('addressFormMethod').value = "PUT";
        
        document.getElementById('modal_recipient_name').value = addr.recipient_name || '';
        document.getElementById('modal_phone').value = addr.phone || '';
        document.getElementById('modal_address_line1').value = addr.address_line1 || '';
        document.getElementById('modal_address_line2').value = addr.address_line2 || '';
        document.getElementById('modal_city').value = addr.city || '';
        document.getElementById('modal_region').value = addr.region || '';
        document.getElementById('modal_postal_code').value = addr.postal_code || '';
        document.getElementById('modal_country').value = addr.country || 'Canada';
        document.getElementById('modal_is_default').checked = !!addr.is_default;

        const modal = new bootstrap.Modal(document.getElementById('userAddressModal'));
        modal.show();
    }

    function toggleFollowCreator(creatorId) {
        const btn = document.getElementById('followBtn_' + creatorId);
        if (!btn) return;

        btn.disabled = true;
        btn.textContent = '...';

        fetch(`/follow/toggle/${creatorId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (data.following) {
                btn.textContent = 'Following';
                btn.className = 'btn-unfollow';
            } else {
                btn.textContent = '+ Follow';
                btn.className = 'btn-unfollow not-following';
            }
        })
        .catch(err => {
            btn.disabled = false;
            console.error('Follow error:', err);
        });
    }
</script>
@endsection
