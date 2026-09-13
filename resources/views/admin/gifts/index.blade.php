@extends('layouts.admin')

@section('title', 'Gifts Management - Super Admin')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">🎁 Live Gifts Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Create and manage interactive live stream & chat gifts, coin pricing, upload icon images (JPG, PNG, GIF, SVG, WEBP), and tier categories.</p>
    </div>
    <button onclick="document.getElementById('createGiftModal').style.display='flex'" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
        <i class="bi bi-plus-lg"></i> Add New Gift
    </button>
</div>

<!-- SEARCH & FILTER BAR -->
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
    <form method="GET" action="{{ route('admin.gifts.index') }}" style="display: flex; gap: 12px; flex: 1; max-width: 600px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search gifts by name..." style="flex: 1; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
        <select name="tier" style="background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
            <option value="">All Tiers</option>
            <option value="standard" {{ request('tier') === 'standard' ? 'selected' : '' }}>Standard</option>
            <option value="vip" {{ request('tier') === 'vip' ? 'selected' : '' }}>VIP</option>
            <option value="exclusive" {{ request('tier') === 'exclusive' ? 'selected' : '' }}>Exclusive</option>
        </select>
        <button type="submit" style="background: var(--cyan-accent); color: #090D10; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            <i class="bi bi-search"></i> Filter
        </button>
    </form>
    <div style="font-size: 14px; color: var(--text-muted);">
        Total: <strong style="color: #fff;">{{ $gifts->total() }}</strong> Gifts
    </div>
</div>

<!-- GIFTS TABLE -->
<div class="admin-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Icon / Image</th>
                <th style="padding: 16px 20px;">Gift Name & Slug</th>
                <th style="padding: 16px 20px;">Coin Price</th>
                <th style="padding: 16px 20px;">Animation Effect</th>
                <th style="padding: 16px 20px;">Tier Level</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($gifts as $g)
                @php
                    $iconSrc = '';
                    if (!empty($g->icon_url)) {
                        if (str_starts_with($g->icon_url, 'http') || str_starts_with($g->icon_url, '/uploads') || str_starts_with($g->icon_url, '/storage')) {
                            $iconSrc = $g->icon_url;
                        } else {
                            $iconSrc = asset($g->icon_url);
                        }
                    } else {
                        $iconSrc = asset('assets/gifts/heart.svg');
                    }
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                            <img src="{{ $iconSrc }}" alt="{{ $g->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 700; color: #fff; font-size: 15px;">{{ $g->name }}</div>
                        <div style="font-size: 12px; color: var(--text-muted);">{{ $g->slug }}</div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; font-weight: 800; font-size: 14px; padding: 4px 12px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                            🪙 {{ number_format($g->coin_cost) }}
                        </span>
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(37, 244, 238, 0.12); color: var(--cyan-accent); font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                            ✨ {{ $g->animation_type ?: 'sparkle' }}
                        </span>
                    </td>
                    <td style="padding: 16px 20px;">
                        @if($g->tier_ladder === 'exclusive')
                            <span style="background: rgba(254, 44, 85, 0.15); color: #FE2C55; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                👑 Exclusive
                            </span>
                        @elseif($g->tier_ladder === 'vip')
                            <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                ⭐ VIP
                            </span>
                        @else
                            <span style="background: rgba(255, 255, 255, 0.08); color: var(--text-muted); font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                🔹 Standard
                            </span>
                        @endif
                    </td>
                    <td style="padding: 16px 20px;">
                        <form method="POST" action="{{ route('admin.gifts.toggle', $g->id) }}">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                @if($g->is_active)
                                    <span style="background: rgba(37, 244, 238, 0.15); color: var(--cyan-accent); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--cyan-accent);"></span> Active
                                    </span>
                                @else
                                    <span style="background: rgba(255, 255, 255, 0.08); color: var(--text-muted); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--text-muted);"></span> Inactive
                                    </span>
                                @endif
                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button onclick="openEditGiftModal({{ json_encode($g) }}, '{{ $iconSrc }}')" style="background: rgba(37, 244, 238, 0.1); color: var(--cyan-accent); border: 1px solid rgba(37, 244, 238, 0.2); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.gifts.delete', $g->id) }}" onsubmit="return confirm('Are you sure you want to delete this gift?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(254, 44, 85, 0.1); color: #FE2C55; border: 1px solid rgba(254, 44, 85, 0.2); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                        No gifts found. Click "Add New Gift" to create one!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        {{ $gifts->links() }}
    </div>
</div>

<!-- CREATE GIFT MODAL -->
<div id="createGiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin: 0;">🎁 Add New Live Gift</h2>
            <button onclick="document.getElementById('createGiftModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.gifts.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Gift Name *</label>
                <input type="text" name="name" required placeholder="e.g. Star Wink, Cyber Dragon, Golden Trophy" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Coin Cost (Price in Coins) *</label>
                <input type="number" name="coin_cost" required min="1" placeholder="e.g. 50" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Tier Level *</label>
                    <select name="tier_ladder" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="standard">Standard (Low Tier)</option>
                        <option value="vip">VIP (Mid Tier)</option>
                        <option value="exclusive">Exclusive (High Tier)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Animation Effect *</label>
                    <select name="animation_type" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="sparkle">✨ Sparkle</option>
                        <option value="heart">❤️ Floating Heart</option>
                        <option value="burst">💥 Energy Burst</option>
                        <option value="wave">🌊 Golden Wave</option>
                        <option value="crown">👑 Royal Crown</option>
                        <option value="dragon">🐉 Dragon Ascend</option>
                        <option value="cosmic">🌌 Cosmic Blast</option>
                    </select>
                </div>
            </div>

            <!-- Single File Upload Input -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #fff; margin-bottom: 6px; font-weight: 700;">
                    <i class="bi bi-cloud-arrow-up-fill" style="color: var(--pink-accent);"></i> Upload Gift Icon / Image (JPG, PNG, GIF, SVG, WEBP) *
                </label>
                <input type="file" name="icon_file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/*" required onchange="previewCreateGiftIcon(this)" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                
                <div id="createGiftIconPreviewContainer" style="display: none; margin-top: 12px; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; color: var(--text-muted);">Preview:</span>
                    <div style="width: 54px; height: 54px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--pink-accent); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                        <img id="createGiftIconPreview" src="" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" value="1" id="createIsActive" checked style="width: 18px; height: 18px;">
                <label for="createIsActive" style="color: #fff; font-size: 14px; cursor: pointer;">Active (Available in live streams)</label>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('createGiftModal').style.display='none'" style="background: rgba(255,255,255,0.06); color: #fff; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">Save Gift</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT GIFT MODAL -->
<div id="editGiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin: 0;">✏️ Edit Live Gift</h2>
            <button onclick="document.getElementById('editGiftModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">✕</button>
        </div>

        <form id="editGiftForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Gift Name *</label>
                <input type="text" name="name" id="editGiftName" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Coin Cost *</label>
                <input type="number" name="coin_cost" id="editGiftCost" required min="1" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Tier Level *</label>
                    <select name="tier_ladder" id="editGiftTier" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="standard">Standard</option>
                        <option value="vip">VIP</option>
                        <option value="exclusive">Exclusive</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Animation Effect *</label>
                    <select name="animation_type" id="editGiftAnimation" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="sparkle">✨ Sparkle</option>
                        <option value="heart">❤️ Floating Heart</option>
                        <option value="burst">💥 Energy Burst</option>
                        <option value="wave">🌊 Golden Wave</option>
                        <option value="crown">👑 Royal Crown</option>
                        <option value="dragon">🐉 Dragon Ascend</option>
                        <option value="cosmic">🌌 Cosmic Blast</option>
                    </select>
                </div>
            </div>

            <!-- Single File Upload Input for Edit -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #fff; margin-bottom: 6px; font-weight: 700;">
                    <i class="bi bi-cloud-arrow-up-fill" style="color: var(--cyan-accent);"></i> Replace Icon Image (JPG, PNG, GIF, SVG, WEBP)
                </label>
                <input type="file" name="icon_file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/*" onchange="previewEditGiftIcon(this)" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px; border-radius: 8px; font-size: 13px; box-sizing: border-box;">
                
                <div style="margin-top: 12px; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; color: var(--text-muted);">Current Icon:</span>
                    <div style="width: 54px; height: 54px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--cyan-accent); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px;">
                        <img id="editGiftIconPreview" src="" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" value="1" id="editGiftIsActive" style="width: 18px; height: 18px;">
                <label for="editGiftIsActive" style="color: #fff; font-size: 14px; cursor: pointer;">Active</label>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editGiftModal').style.display='none'" style="background: rgba(255,255,255,0.06); color: #fff; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 700; cursor: pointer;">Update Gift</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditGiftModal(gift, iconSrc) {
        document.getElementById('editGiftForm').action = '/admin/gifts/' + gift.id;
        document.getElementById('editGiftName').value = gift.name || '';
        document.getElementById('editGiftCost').value = gift.coin_cost || 10;
        document.getElementById('editGiftTier').value = gift.tier_ladder || 'standard';
        document.getElementById('editGiftAnimation').value = gift.animation_type || 'sparkle';
        document.getElementById('editGiftIsActive').checked = !!gift.is_active;
        document.getElementById('editGiftIconPreview').src = iconSrc || '/assets/gifts/heart.svg';
        document.getElementById('editGiftModal').style.display = 'flex';
    }

    function previewCreateGiftIcon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('createGiftIconPreview').src = e.target.result;
                document.getElementById('createGiftIconPreviewContainer').style.display = 'flex';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEditGiftIcon(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editGiftIconPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
