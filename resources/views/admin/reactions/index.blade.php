@extends('layouts.admin')

@section('title', 'Reactions & GIFs Management - Super Admin')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">✨ Reactions & GIFs Management</h1>
        <p style="font-size: 14px; color: var(--text-muted);">Manage live room chat reactions, quick emojis, and upload animated GIFs or reaction images (JPG, PNG, GIF, WEBP, SVG).</p>
    </div>
    <button onclick="document.getElementById('createReactionModal').style.display='flex'" style="background: linear-gradient(135deg, var(--cyan-accent), #1ed6d0); color: #090D10; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 244, 238, 0.4);">
        <i class="bi bi-plus-lg"></i> Add New Reaction / GIF
    </button>
</div>

<!-- SEARCH & FILTER BAR -->
<div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
    <form method="GET" action="{{ route('admin.reactions.index') }}" style="display: flex; gap: 12px; flex: 1; max-width: 600px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or emoji..." style="flex: 1; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
        <select name="type" style="background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px;">
            <option value="">All Types</option>
            <option value="emoji" {{ request('type') === 'emoji' ? 'selected' : '' }}>Emojis / Reactions</option>
            <option value="gif" {{ request('type') === 'gif' ? 'selected' : '' }}>Animated GIFs</option>
        </select>
        <button type="submit" style="background: var(--cyan-accent); color: #090D10; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            <i class="bi bi-search"></i> Filter
        </button>
    </form>
    <div style="font-size: 14px; color: var(--text-muted);">
        Total: <strong style="color: #fff;">{{ $reactions->total() }}</strong> Items
    </div>
</div>

<!-- REACTIONS TABLE -->
<div class="admin-table-container">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
                <th style="padding: 16px 20px;">Preview</th>
                <th style="padding: 16px 20px;">Name</th>
                <th style="padding: 16px 20px;">Type</th>
                <th style="padding: 16px 20px;">Emoji / Media</th>
                <th style="padding: 16px 20px;">Order</th>
                <th style="padding: 16px 20px;">Status</th>
                <th style="padding: 16px 20px; text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reactions as $r)
                @php
                    $mediaSrc = '';
                    if (!empty($r->media_url)) {
                        if (str_starts_with($r->media_url, 'http') || str_starts_with($r->media_url, '/uploads') || str_starts_with($r->media_url, '/storage')) {
                            $mediaSrc = $r->media_url;
                        } else {
                            $mediaSrc = asset($r->media_url);
                        }
                    }
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); color: #fff;">
                    <td style="padding: 16px 20px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 26px;">
                            @if($mediaSrc)
                                <img src="{{ $mediaSrc }}" alt="{{ $r->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <span>{{ $r->code ?: '✨' }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 16px 20px;">
                        <div style="font-weight: 700; color: #fff; font-size: 15px;">{{ $r->name }}</div>
                    </td>
                    <td style="padding: 16px 20px;">
                        @if($r->type === 'gif')
                            <span style="background: rgba(254, 44, 85, 0.15); color: #FE2C55; font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                🎬 Animated GIF
                            </span>
                        @else
                            <span style="background: rgba(37, 244, 238, 0.15); color: var(--cyan-accent); font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;">
                                😃 Emoji / Reaction
                            </span>
                        @endif
                    </td>
                    <td style="padding: 16px 20px;">
                        @if($r->code)
                            <code style="background: rgba(255,255,255,0.06); padding: 4px 8px; border-radius: 4px; font-size: 16px; color: #fff;">{{ $r->code }}</code>
                        @endif
                        @if($mediaSrc)
                            <a href="{{ $mediaSrc }}" target="_blank" style="color: var(--cyan-accent); text-decoration: none; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; margin-left: 4px;">
                                <i class="bi bi-box-arrow-up-right"></i> View File
                            </a>
                        @endif
                    </td>
                    <td style="padding: 16px 20px; color: var(--text-muted);">
                        {{ $r->sort_order }}
                    </td>
                    <td style="padding: 16px 20px;">
                        <form method="POST" action="{{ route('admin.reactions.toggle', $r->id) }}">
                            @csrf
                            <button type="submit" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                @if($r->is_active)
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
                            <button onclick="openEditReactionModal({{ json_encode($r) }}, '{{ $mediaSrc }}')" style="background: rgba(37, 244, 238, 0.1); color: var(--cyan-accent); border: 1px solid rgba(37, 244, 238, 0.2); padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.reactions.delete', $r->id) }}" onsubmit="return confirm('Are you sure you want to delete this reaction?');">
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
                        No reactions found. Click "Add New Reaction / GIF" to create one!
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
        {{ $reactions->links() }}
    </div>
</div>

<!-- CREATE REACTION MODAL -->
<div id="createReactionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin: 0;">✨ Add Reaction / GIF</h2>
            <button onclick="document.getElementById('createReactionModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.reactions.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Name *</label>
                <input type="text" name="name" required placeholder="e.g. Fire, Heart Eyes, Dance GIF" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Type *</label>
                    <select name="type" id="createTypeSelect" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="emoji">😃 Emoji / Reaction</option>
                        <option value="gif">🎬 Animated GIF</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Sort Order</label>
                    <input type="number" name="sort_order" value="0" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Emoji Character (for Emoji Type)</label>
                <input type="text" name="code" placeholder="🔥" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 18px; box-sizing: border-box;">
            </div>

            <!-- Single File Upload Input -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #fff; margin-bottom: 6px; font-weight: 700;">
                    <i class="bi bi-cloud-arrow-up-fill" style="color: var(--cyan-accent);"></i> Upload GIF / Image File (JPG, PNG, GIF, WEBP, SVG)
                </label>
                <input type="file" name="media_file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/*,image/gif" onchange="previewCreateReactionFile(this)" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px; border-radius: 8px; font-size: 13px; box-sizing: border-box;">

                <div id="createReactionPreviewContainer" style="display: none; margin-top: 12px; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; color: var(--text-muted);">Preview:</span>
                    <div style="width: 54px; height: 54px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--cyan-accent); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="createReactionPreview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" value="1" id="createReactionIsActive" checked style="width: 18px; height: 18px;">
                <label for="createReactionIsActive" style="color: #fff; font-size: 14px; cursor: pointer;">Active (Visible in live room tooltip & chat)</label>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('createReactionModal').style.display='none'" style="background: rgba(255,255,255,0.06); color: #fff; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent), #1ed6d0); color: #090D10; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 800; cursor: pointer;">Save Reaction</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT REACTION MODAL -->
<div id="editReactionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 10px 40px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; font-weight: 800; color: #fff; margin: 0;">✏️ Edit Reaction / GIF</h2>
            <button onclick="document.getElementById('editReactionModal').style.display='none'" style="background: none; border: none; color: var(--text-muted); font-size: 20px; cursor: pointer;">✕</button>
        </div>

        <form id="editReactionForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Name *</label>
                <input type="text" name="name" id="editReactionName" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Type *</label>
                    <select name="type" id="editReactionType" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="emoji">Emoji / Reaction</option>
                        <option value="gif">Animated GIF</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Sort Order</label>
                    <input type="number" name="sort_order" id="editReactionSortOrder" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600;">Emoji Code / Text</label>
                <input type="text" name="code" id="editReactionCode" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 18px; box-sizing: border-box;">
            </div>

            <!-- Single File Upload Input for Edit -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; color: #fff; margin-bottom: 6px; font-weight: 700;">
                    <i class="bi bi-cloud-arrow-up-fill" style="color: var(--cyan-accent);"></i> Replace GIF / Image (JPG, PNG, GIF, WEBP, SVG)
                </label>
                <input type="file" name="media_file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,image/*,image/gif" onchange="previewEditReactionFile(this)" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px; border-radius: 8px; font-size: 13px; box-sizing: border-box;">

                <div id="editReactionPreviewContainer" style="margin-top: 12px; display: none; align-items: center; gap: 12px;">
                    <span style="font-size: 12px; color: var(--text-muted);">Current Media:</span>
                    <div style="width: 54px; height: 54px; border-radius: 12px; background: #0c0d14; border: 1px solid var(--cyan-accent); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        <img id="editReactionPreview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" value="1" id="editReactionIsActive" style="width: 18px; height: 18px;">
                <label for="editReactionIsActive" style="color: #fff; font-size: 14px; cursor: pointer;">Active</label>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editReactionModal').style.display='none'" style="background: rgba(255,255,255,0.06); color: #fff; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent), #1ed6d0); color: #090D10; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 800; cursor: pointer;">Update Reaction</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditReactionModal(item, mediaSrc) {
        document.getElementById('editReactionForm').action = '/admin/reactions/' + item.id;
        document.getElementById('editReactionName').value = item.name || '';
        document.getElementById('editReactionType').value = item.type || 'emoji';
        document.getElementById('editReactionCode').value = item.code || '';
        document.getElementById('editReactionSortOrder').value = item.sort_order || 0;
        document.getElementById('editReactionIsActive').checked = !!item.is_active;
        
        if (mediaSrc) {
            document.getElementById('editReactionPreview').src = mediaSrc;
            document.getElementById('editReactionPreviewContainer').style.display = 'flex';
        } else {
            document.getElementById('editReactionPreviewContainer').style.display = 'none';
        }

        document.getElementById('editReactionModal').style.display = 'flex';
    }

    function previewCreateReactionFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('createReactionPreview').src = e.target.result;
                document.getElementById('createReactionPreviewContainer').style.display = 'flex';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewEditReactionFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('editReactionPreview').src = e.target.result;
                document.getElementById('editReactionPreviewContainer').style.display = 'flex';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
