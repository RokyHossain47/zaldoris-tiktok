@extends('layouts.admin')

@section('title', 'Banner Management - Super Admin')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Banner Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage homepage and marketplace advertisement banners, target URLs, and display placements.</p>
    </div>
    <button onclick="document.getElementById('createBannerModal').style.display='flex'" style="background: linear-gradient(135deg, var(--pink-accent), var(--pink-hover)); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
        <i class="bi bi-plus-lg"></i> Add New Banner
    </button>
</div>

<!-- BANNERS TABLE -->
<div class="admin-table-container">
    <div class="table-header-bar">
        <div style="font-weight: 700; font-size: 16px; color: #fff;">
            All Active & Scheduled Banners ({{ $banners->total() }})
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Banner Preview</th>
                <th style="padding: 16px 20px;">Title & Subtitle</th>
                <th style="padding: 16px 20px;">Placement / Location</th>
                <th style="padding: 16px 20px;">Target Link</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($banners as $b)
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <img src="{{ $b->media_url }}" alt="{{ $b->title }}" style="width: 120px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 700; color: #fff; max-width: 250px;">{{ $b->title }}</div>
                        @if($b->subtitle)
                            <div style="font-size: 12px; color: var(--text-muted);">{{ Str::limit($b->subtitle, 40) }}</div>
                        @endif
                    </td>
                    <td style="padding: 16px 20px;">
                        <span style="background: rgba(37, 244, 238, 0.15); color: var(--cyan-accent); font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                            @if($b->placement === 'homepage_banner')
                                🏠 Homepage Banner
                            @elseif($b->placement === 'homepage_sidebar')
                                📌 Homepage Sidebar
                            @elseif($b->placement === 'shop_banner')
                                🛍️ Shop Banner
                            @else
                                📺 Live Stream Banner
                            @endif
                        </span>
                    </td>
                    <td style="padding: 16px 20px;">
                        <a href="{{ $b->link_url }}" target="_blank" style="color: var(--cyan-accent); text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                            {{ Str::limit($b->link_url, 30) }} <i class="bi bi-box-arrow-up-right" style="font-size: 10px;"></i>
                        </a>
                    </td>
                    <td style="padding: 16px 20px;">
                        <form action="{{ route('admin.banners.toggle', $b->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="status-pill {{ $b->is_active ? 'status-active' : 'status-blocked' }}" style="border: none; cursor: pointer;" title="Click to toggle status">
                                {{ $b->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: 16px 20px; text-align: right;">
                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                            <button onclick="openEditModal({{ json_encode($b) }})" class="action-btn" title="Edit Banner">
                                <i class="bi bi-pencil-fill" style="color: var(--cyan-accent);"></i>
                            </button>
                            <form action="{{ route('admin.banners.delete', $b->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this banner?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Delete Banner">
                                    <i class="bi bi-trash-fill" style="color: var(--pink-accent);"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted);">
                        No banners created yet. Click "Add New Banner" to create one.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px;">
        {{ $banners->links() }}
    </div>
</div>

<!-- CREATE BANNER MODAL -->
<div id="createBannerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff;">Create New Banner</h2>
            <button onclick="document.getElementById('createBannerModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Banner Title *</label>
                <input type="text" name="title" required placeholder="e.g. Mega Spring Live Drops & Exclusive Raffles" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Subtitle / Description</label>
                <input type="text" name="subtitle" placeholder="e.g. Live unboxing & instant 1-tap checkout" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Where will this banner show? (Placement) *</label>
                <select name="placement" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                    <option value="homepage_banner">🏠 Homepage Main Banner (Top Hero)</option>
                    <option value="homepage_sidebar">📌 Homepage Sidebar / Promo Card</option>
                    <option value="shop_banner">🛍️ Shop / Live Commerce Banner</option>
                    <option value="live_stream_banner">📺 Live Stream Video Room Ad</option>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Upload Banner Image</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Or provide Image URL below if not uploading a file:</div>
            </div>

            <div style="margin-bottom: 16px;">
                <input type="url" name="media_url" placeholder="https://images.unsplash.com/..." style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Target Link URL</label>
                    <input type="text" name="link_url" placeholder="/live-shopping or https://..." value="/live-shopping" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Button Text</label>
                    <input type="text" name="button_text" placeholder="e.g. Shop Now / Get Deal" value="Shop Now" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('createBannerModal').style.display='none'" style="background: rgba(255,255,255,0.08); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: var(--pink-accent); color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; cursor: pointer;">Create Banner</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT BANNER MODAL -->
<div id="editBannerModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff;">Edit Banner</h2>
            <button onclick="document.getElementById('editBannerModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 22px; cursor: pointer;">&times;</button>
        </div>

        <form id="editBannerForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Banner Title *</label>
                <input type="text" id="edit_title" name="title" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Subtitle / Description</label>
                <input type="text" id="edit_subtitle" name="subtitle" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Placement / Location *</label>
                <select id="edit_placement" name="placement" required style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                    <option value="homepage_banner">🏠 Homepage Main Banner (Top Hero)</option>
                    <option value="homepage_sidebar">📌 Homepage Sidebar / Promo Card</option>
                    <option value="shop_banner">🛍️ Shop / Live Commerce Banner</option>
                    <option value="live_stream_banner">📺 Live Stream Video Room Ad</option>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Replace Image (Optional)</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Or Image URL</label>
                <input type="url" id="edit_media_url" name="media_url" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Target Link URL</label>
                    <input type="text" id="edit_link_url" name="link_url" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px;">Button Text</label>
                    <input type="text" id="edit_button_text" name="button_text" style="width: 100%; background: var(--bg-card-inner); border: 1px solid var(--border-color); color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 14px;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('editBannerModal').style.display='none'" style="background: rgba(255,255,255,0.08); color: #fff; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: var(--cyan-accent); color: #13141f; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; cursor: pointer;">Update Banner</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(banner) {
    document.getElementById('edit_title').value = banner.title || '';
    document.getElementById('edit_subtitle').value = banner.subtitle || '';
    document.getElementById('edit_placement').value = banner.placement || 'homepage_banner';
    document.getElementById('edit_media_url').value = banner.media_url || '';
    document.getElementById('edit_link_url').value = banner.link_url || '';
    document.getElementById('edit_button_text').value = banner.button_text || '';
    
    document.getElementById('editBannerForm').action = '/admin/banners/' + banner.id;
    document.getElementById('editBannerModal').style.display = 'flex';
}
</script>
@endsection
