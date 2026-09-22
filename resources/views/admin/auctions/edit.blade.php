@extends('layouts.admin')

@section('title', 'Edit Auction - Super Admin')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Edit Live Auction</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Update details, adjust bidding parameters, extend countdown timer, or modify status.</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('auctions.show', $auction->id) }}" target="_blank" style="background: rgba(37, 244, 238, 0.1); border: 1px solid rgba(37, 244, 238, 0.3); color: var(--cyan-accent); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-box-arrow-up-right"></i> View Live Page
            </a>
            <a href="{{ route('admin.auctions.index') }}" style="color: var(--text-muted); text-decoration: none; display: flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600;">
                <i class="bi bi-arrow-left"></i> Back to Auctions
            </a>
        </div>
    </div>

    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid var(--pink-accent); color: #fff; border-radius: 10px; padding: 14px 18px; margin-bottom: 24px;">
            <div style="font-weight: 700; margin-bottom: 6px; color: var(--pink-accent);">Please correct the following errors:</div>
            <ul style="margin-left: 20px; font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.auctions.update', $auction->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- BASIC DETAILS -->
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Auction Title *</label>
                <input type="text" name="title" value="{{ old('title', $auction->title) }}" required placeholder="e.g. Vintage Rolex Submariner 1974 'Pre-Comex'" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Seller / Host Creator *</label>
                    <select name="seller_id" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        @foreach($sellers as $s)
                            <option value="{{ $s->id }}" {{ old('seller_id', $auction->seller_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->name }} ({{ '@' . $s->username }} - {{ ucfirst($s->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Link to Catalog Product (Optional)</label>
                    <select name="product_id" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="">-- No specific catalog product --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id', $auction->product_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->title }} ({{ setting('currency_symbol', '$') }}{{ number_format($p->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- PRICING & BID STEPPING -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">Starting Bid ({{ setting('currency_symbol', '$') }}) *</label>
                    <input type="number" step="0.01" min="0.01" name="starting_bid" value="{{ old('starting_bid', $auction->starting_bid) }}" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">Current High Bid ({{ setting('currency_symbol', '$') }})</label>
                    <input type="number" step="0.01" min="0.01" name="current_bid" value="{{ old('current_bid', $auction->current_bid) }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: var(--cyan-accent); font-weight: 800; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">Reserve Price ({{ setting('currency_symbol', '$') }})</label>
                    <input type="number" step="0.01" min="0" name="reserve_price" value="{{ old('reserve_price', $auction->reserve_price) }}" placeholder="Optional threshold" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px;">Bid Step ({{ setting('currency_symbol', '$') }}) *</label>
                    <input type="number" step="0.01" min="0.01" name="min_bid_step" value="{{ old('min_bid_step', $auction->min_bid_step) }}" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <!-- SCHEDULE TIMINGS & STATUS -->
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Starts At</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $auction->starts_at ? $auction->starts_at->format('Y-m-d\TH:i') : '') }}" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Ends At *</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $auction->ends_at ? $auction->ends_at->format('Y-m-d\TH:i') : '') }}" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Status *</label>
                    <select name="status" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px;">
                        <option value="active" {{ old('status', $auction->status) === 'active' ? 'selected' : '' }}>🔴 Active Live Now</option>
                        <option value="upcoming" {{ old('status', $auction->status) === 'upcoming' ? 'selected' : '' }}>⏳ Upcoming Drop</option>
                        <option value="sold" {{ old('status', $auction->status) === 'sold' ? 'selected' : '' }}>✅ Sold / Completed</option>
                        <option value="failed" {{ old('status', $auction->status) === 'failed' ? 'selected' : '' }}>❌ Failed / Expired</option>
                        <option value="cancelled" {{ old('status', $auction->status) === 'cancelled' ? 'selected' : '' }}>⛔ Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- BLUR ON EXPIRY FLAG -->
            <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: #fff; font-size: 14px; font-weight: 600;">
                    <input type="checkbox" name="is_blurred" value="1" {{ old('is_blurred', $auction->is_blurred) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--pink-accent);">
                    <span>🔒 Blur listing details (SRS listing blur restriction when expired)</span>
                </label>
            </div>

            <!-- IMAGE PREVIEW & EDIT -->
            <div style="margin-bottom: 24px; background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Auction Main Image</label>
                
                @if($auction->image_url)
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px; background: var(--bg-card); padding: 12px; border-radius: 10px; border: 1px solid var(--border-color);">
                        <img src="{{ $auction->image_url }}" alt="Current Image" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                        <div>
                            <div style="font-size: 12px; font-weight: 700; color: #fff;">Current Image</div>
                            <div style="font-size: 11px; color: var(--text-muted); word-break: break-all;">{{ $auction->image_url }}</div>
                        </div>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Upload New Image (Replaces current):</div>
                        <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">Or Update Image URL:</div>
                        <input type="url" name="image_url" value="{{ old('image_url', $auction->image_url) }}" placeholder="https://images.unsplash.com/..." style="width: 100%; background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 8px; font-size: 13px;">
                    </div>
                </div>
            </div>

            <!-- DESCRIPTION -->
            <div style="margin-bottom: 28px;">
                <label style="display: block; font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 8px;">Detailed Description & Authenticity Notes</label>
                <textarea name="description" rows="5" placeholder="Include serial number, condition grade, accessories, and shipping provenance details..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 14px 16px; border-radius: 10px; font-size: 14px; line-height: 1.5;">{{ old('description', $auction->description) }}</textarea>
            </div>

            <!-- BIDS HISTORY SUMMARY -->
            @if($auction->bids->count() > 0)
                <div style="background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h3 style="font-size: 15px; font-weight: 800; color: #fff;">Bid History ({{ $auction->bids->count() }} Total Bids)</h3>
                        <span style="font-size: 12px; color: var(--cyan-accent); font-weight: 700;">Latest First</span>
                    </div>
                    <div style="max-height: 200px; overflow-y: auto;">
                        <table style="width: 100%; font-size: 13px; text-align: left;">
                            <thead>
                                <tr style="color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                                    <th style="padding: 6px 10px;">Bidder</th>
                                    <th style="padding: 6px 10px;">Amount</th>
                                    <th style="padding: 6px 10px;">Time</th>
                                    <th style="padding: 6px 10px;">IP / Anti-Shill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auction->bids as $b)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); color: #fff;">
                                        <td style="padding: 8px 10px;">{{ $b->user ? $b->user->name : 'Unknown' }}</td>
                                        <td style="padding: 8px 10px; font-weight: 700; color: var(--cyan-accent);">{{ setting('currency_symbol', '$') }}{{ number_format($b->amount, 2) }}</td>
                                        <td style="padding: 8px 10px; color: var(--text-muted);">{{ $b->created_at->diffForHumans() }}</td>
                                        <td style="padding: 8px 10px;">
                                            @if($b->is_flagged_shill)
                                                <span style="color: var(--pink-accent); font-weight: 700;">⚠️ Flagged Shill</span>
                                            @else
                                                <span style="color: #28a745;">✓ Verified</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- BUTTONS -->
            <div style="display: flex; gap: 16px; justify-content: flex-end; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <a href="{{ route('admin.auctions.index') }}" style="background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent), #00d2c7); color: #000; border: none; padding: 12px 32px; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 244, 238, 0.4);">
                    <i class="bi bi-check-lg"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
