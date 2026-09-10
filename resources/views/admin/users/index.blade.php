@extends('layouts.admin')

@section('title', 'User Management - GenZ Live Admin')

@push('admin_styles')
<style>
    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-backdrop-custom.show {
        display: flex;
    }
    .modal-box-custom {
        background: #1c1d2e;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        width: 90%;
        max-width: 580px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    }
    .form-group-custom {
        margin-bottom: 16px;
    }
    .form-label-custom {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .form-control-custom {
        width: 100%;
        background: #161725;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 10px 14px;
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }
    .form-control-custom:focus {
        border-color: var(--pink-accent);
    }
    .role-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .role-buyer { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
    .role-creator { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
    .role-seller { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .role-moderator { background: rgba(37, 244, 238, 0.15); color: #25F4EE; }
    .role-admin { background: rgba(254, 44, 85, 0.15); color: #FE2C55; }
    .role-dispute_manager { background: rgba(249, 115, 22, 0.15); color: #fb923c; }

    .action-btn-sm {
        background: #161725;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #fff;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .action-btn-sm:hover {
        background: #23253a;
        color: #fff;
    }
    .action-btn-edit:hover {
        border-color: var(--cyan-accent);
        color: var(--cyan-accent);
    }
    .action-btn-ban {
        border-color: rgba(254, 44, 85, 0.3);
        color: #FE2C55;
    }
    .action-btn-ban:hover {
        background: rgba(254, 44, 85, 0.15);
        color: #FE2C55;
    }
    .action-btn-unban {
        border-color: rgba(39, 201, 137, 0.3);
        color: #27c989;
    }
    .action-btn-unban:hover {
        background: rgba(39, 201, 137, 0.15);
        color: #27c989;
    }
    .action-btn-delete:hover {
        background: #FE2C55;
        border-color: #FE2C55;
        color: #fff;
    }
</style>
@endpush

@section('content')

    <!-- TOP HEADER ROW -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <span style="font-size: 24px; color: var(--pink-accent);"><i class="bi bi-people-fill"></i></span>
                <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin: 0;">User Management</h1>
            </div>
            <p style="color: var(--text-muted); font-size: 13px;">Manage, edit, create, ban, unban, and monitor all platform accounts.</p>
        </div>

        <div style="display: flex; gap: 10px;">
            <button onclick="openModal('modalAddUser')" style="background: var(--pink-accent); color: #fff; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(254, 44, 85, 0.4);">
                <i class="bi bi-person-plus-fill"></i> Add New User
            </button>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div style="background: rgba(39, 201, 137, 0.15); border: 1px solid #27c989; color: #27c989; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: rgba(254, 44, 85, 0.15); border: 1px solid #FE2C55; color: #FE2C55; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 13px;">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:</strong>
            <ul style="margin: 6px 0 0 18px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4 SUMMARY STAT CARDS -->
    <div class="stat-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Users</div>
                <div class="stat-val">{{ number_format($totalUsers) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(254, 44, 85, 0.15); color: #FE2C55;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Active Accounts</div>
                <div class="stat-val">{{ number_format($activeUsers) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(39, 201, 137, 0.15); color: #27c989;">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Banned / Suspended</div>
                <div class="stat-val" style="color: #FE2C55;">{{ number_format($bannedUsers) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(254, 44, 85, 0.15); color: #FE2C55;">
                <i class="bi bi-slash-circle-fill"></i>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">VIP Members</div>
                <div class="stat-val" style="color: #FFB800;">{{ number_format($vipUsers) }}</div>
            </div>
            <div class="stat-icon-wrapper" style="background: rgba(255, 184, 0, 0.15); color: #FFB800;">
                <i class="bi bi-crown-fill"></i>
            </div>
        </div>
    </div>

    <!-- USERS TABLE CONTAINER -->
    <div class="admin-table-container">
        
        <!-- Table Header Filter Bar -->
        <div class="table-header-bar">
            <!-- Filter Pills -->
            <div class="table-filter-pills">
                <a href="{{ route('admin.users.index', ['filter' => 'all', 'role' => $role, 'search' => $search]) }}" class="{{ $filter === 'all' ? 'active' : '' }}">All ({{ $totalUsers }})</a>
                <a href="{{ route('admin.users.index', ['filter' => 'active', 'role' => $role, 'search' => $search]) }}" class="{{ $filter === 'active' ? 'active' : '' }}">Active</a>
                <a href="{{ route('admin.users.index', ['filter' => 'blocked', 'role' => $role, 'search' => $search]) }}" class="{{ in_array($filter, ['blocked', 'banned']) ? 'active' : '' }}">Banned</a>
                <a href="{{ route('admin.users.index', ['filter' => 'hosts', 'role' => $role, 'search' => $search]) }}" class="{{ $filter === 'hosts' ? 'active' : '' }}">Hosts</a>
                <a href="{{ route('admin.users.index', ['filter' => 'vip', 'role' => $role, 'search' => $search]) }}" class="{{ $filter === 'vip' ? 'active' : '' }}">VIP</a>
            </div>

            <!-- Search & Role Dropdown -->
            <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <input type="hidden" name="filter" value="{{ $filter }}">

                <select name="role" onchange="this.form.submit()" class="form-control-custom" style="width: auto; padding: 8px 12px; font-size: 12px; height: 38px;">
                    <option value="">-- All Roles --</option>
                    <option value="buyer" {{ $role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                    <option value="creator" {{ $role === 'creator' ? 'selected' : '' }}>Creator</option>
                    <option value="seller" {{ $role === 'seller' ? 'selected' : '' }}>Seller</option>
                    <option value="moderator" {{ $role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="dispute_manager" {{ $role === 'dispute_manager' ? 'selected' : '' }}>Dispute Manager</option>
                </select>

                <div class="search-box-wrap">
                    <i class="bi bi-search" style="color: var(--text-muted); font-size: 13px;"></i>
                    <input type="text" name="search" placeholder="Search name, email, phone..." value="{{ $search }}">
                </div>

                <button type="submit" style="background: #1f1f2a; border: 1px solid var(--border-color); color: #fff; padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    Search
                </button>

                @if($search || $role || $filter !== 'all')
                    <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted); font-size: 12px; text-decoration: none; padding: 6px 10px;">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table Data -->
        <div style="overflow-x: auto;">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">#ID</th>
                        <th>User Profile</th>
                        <th>Role</th>
                        <th>Coins / Balance</th>
                        <th>Badges</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th style="text-align: right; width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 700; color: var(--text-muted);">#{{ $user->id }}</td>
                            
                            <!-- User Info -->
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                                    <div>
                                        <div style="font-weight: 700; color: #fff; font-size: 14px;">{{ $user->name }}</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">
                                            <span>&#64;{{ $user->username ?? 'user'.$user->id }}</span> &bull; 
                                            <span>{{ $user->email }}</span>
                                        </div>
                                        @if($user->phone)
                                            <div style="font-size: 11px; color: #777;"><i class="bi bi-telephone"></i> {{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td>
                                <span class="role-badge role-{{ $user->role }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>

                            <!-- Coins / Earnings -->
                            <td>
                                <div class="coin-pill-badge">
                                    <i class="bi bi-coin"></i> {{ number_format($user->coin_balance) }}
                                    <span>Coins</span>
                                </div>
                                @if($user->earnings_usd > 0)
                                    <div style="font-size: 11px; color: var(--cyan-accent); margin-top: 4px; font-weight: 700;">
                                        ${{ number_format($user->earnings_usd, 2) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Badges -->
                            <td>
                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                    @if($user->is_vip)
                                        <span style="background: rgba(255, 184, 0, 0.15); color: #FFB800; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">
                                            <i class="bi bi-crown-fill"></i> VIP
                                        </span>
                                    @endif
                                    @if($user->is_verified)
                                        <span style="background: rgba(37, 244, 238, 0.15); color: #25F4EE; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">
                                            <i class="bi bi-patch-check-fill"></i> VERIFIED
                                        </span>
                                    @endif
                                    @if($user->fast_shipper_badge)
                                        <span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 800;">
                                            <i class="bi bi-lightning-fill"></i> FAST SHIP
                                        </span>
                                    @endif
                                    @if(!$user->is_vip && !$user->is_verified && !$user->fast_shipper_badge)
                                        <span style="color: #666; font-size: 11px;">Standard</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status (Active / Suspended) -->
                            <td>
                                @if($user->is_suspended)
                                    <span class="status-badge status-blocked">
                                        <i class="bi bi-slash-circle"></i> Banned
                                    </span>
                                @else
                                    <span class="status-badge status-active">
                                        <i class="bi bi-check-circle"></i> Active
                                    </span>
                                @endif
                            </td>

                            <!-- Joined Date -->
                            <td style="font-size: 12px; color: var(--text-muted);">
                                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    
                                    <!-- Edit Button -->
                                    <button onclick="openModal('modalEditUser{{ $user->id }}')" class="action-btn-sm action-btn-edit" title="Edit User">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>

                                    <!-- Quick Coins Button -->
                                    <button onclick="openModal('modalCoinsUser{{ $user->id }}')" class="action-btn-sm" title="Adjust Coins" style="color: #FFB800;">
                                        <i class="bi bi-coin"></i>
                                    </button>

                                    <!-- Ban / Unban Toggle Form -->
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.toggle_block', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to {{ $user->is_suspended ? 'UNBAN' : 'BAN' }} user {{ $user->name }}?');">
                                            @csrf
                                            @if($user->is_suspended)
                                                <button type="submit" class="action-btn-sm action-btn-unban" title="Unban User">
                                                    <i class="bi bi-unlock-fill"></i> Unban
                                                </button>
                                            @else
                                                <button type="submit" class="action-btn-sm action-btn-ban" title="Ban User">
                                                    <i class="bi bi-slash-circle-fill"></i> Ban
                                                </button>
                                            @endif
                                        </form>

                                        <!-- Delete Form -->
                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to permanently DELETE user {{ $user->name }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn-sm action-btn-delete" title="Delete User">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 11px; color: var(--pink-accent); font-weight: 700; padding: 4px 8px;">(You)</span>
                                    @endif

                                </div>
                            </td>
                        </tr>

                        <!-- EDIT USER MODAL -->
                        <div id="modalEditUser{{ $user->id }}" class="modal-backdrop-custom">
                            <div class="modal-box-custom">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                                    <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0;">
                                        <i class="bi bi-pencil-square" style="color: var(--cyan-accent);"></i> Edit User: {{ $user->name }}
                                    </h3>
                                    <button type="button" onclick="closeModal('modalEditUser{{ $user->id }}')" style="background: none; border: none; color: #aaa; font-size: 20px; cursor: pointer;">&times;</button>
                                </div>

                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Full Name *</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control-custom" required>
                                        </div>

                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Username *</label>
                                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control-custom" required>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Email Address *</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control-custom" required>
                                        </div>

                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Phone Number</label>
                                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control-custom">
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                                        <div class="form-group-custom">
                                            <label class="form-label-custom">User Role *</label>
                                            <select name="role" class="form-control-custom" required>
                                                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                                                <option value="creator" {{ $user->role === 'creator' ? 'selected' : '' }}>Creator</option>
                                                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller</option>
                                                <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Super Admin</option>
                                                <option value="dispute_manager" {{ $user->role === 'dispute_manager' ? 'selected' : '' }}>Dispute Manager</option>
                                            </select>
                                        </div>

                                        <div class="form-group-custom">
                                            <label class="form-label-custom">Coin Balance *</label>
                                            <input type="number" name="coin_balance" value="{{ old('coin_balance', $user->coin_balance) }}" min="0" class="form-control-custom" required>
                                        </div>
                                    </div>

                                    <div class="form-group-custom">
                                        <label class="form-label-custom">Reset Password (Leave blank to keep unchanged)</label>
                                        <input type="password" name="password" placeholder="New Password (min 6 chars)" class="form-control-custom">
                                    </div>

                                    <div style="display: flex; gap: 20px; margin: 16px 0; background: #161725; padding: 12px; border-radius: 8px;">
                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #fff; cursor: pointer;">
                                            <input type="checkbox" name="is_vip" value="1" {{ $user->is_vip ? 'checked' : '' }}> VIP Member
                                        </label>

                                        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #fff; cursor: pointer;">
                                            <input type="checkbox" name="is_verified" value="1" {{ $user->is_verified ? 'checked' : '' }}> Verified Badge
                                        </label>

                                        @if($user->id !== auth()->id())
                                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #FE2C55; font-weight: 700; cursor: pointer;">
                                                <input type="checkbox" name="is_suspended" value="1" {{ $user->is_suspended ? 'checked' : '' }}> Banned / Suspended
                                            </label>
                                        @endif
                                    </div>

                                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                                        <button type="button" onclick="closeModal('modalEditUser{{ $user->id }}')" class="action-btn-sm" style="padding: 10px 18px;">Cancel</button>
                                        <button type="submit" style="background: var(--pink-accent); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- QUICK COIN ADJUST MODAL -->
                        <div id="modalCoinsUser{{ $user->id }}" class="modal-backdrop-custom">
                            <div class="modal-box-custom" style="max-width: 420px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                    <h3 style="font-size: 16px; font-weight: 800; color: #FFB800; margin: 0;">
                                        <i class="bi bi-coin"></i> Adjust Coins: {{ $user->name }}
                                    </h3>
                                    <button type="button" onclick="closeModal('modalCoinsUser{{ $user->id }}')" style="background: none; border: none; color: #aaa; font-size: 20px; cursor: pointer;">&times;</button>
                                </div>

                                <form action="{{ route('admin.users.update_coins', $user->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group-custom">
                                        <label class="form-label-custom">New Total Coin Balance</label>
                                        <input type="number" name="coin_balance" value="{{ $user->coin_balance }}" min="0" class="form-control-custom" required>
                                        <p style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Current Balance: {{ number_format($user->coin_balance) }} coins</p>
                                    </div>

                                    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px;">
                                        <button type="button" onclick="closeModal('modalCoinsUser{{ $user->id }}')" class="action-btn-sm">Cancel</button>
                                        <button type="submit" style="background: #FFB800; color: #000; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer;">
                                            Update Coins
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 50px; color: var(--text-muted);">
                                <i class="bi bi-people" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                No users found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $users->links() }}
        </div>

    </div>

    <!-- CREATE / ADD USER MODAL -->
    <div id="modalAddUser" class="modal-backdrop-custom">
        <div class="modal-box-custom">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <h3 style="font-size: 18px; font-weight: 800; color: #fff; margin: 0;">
                    <i class="bi bi-person-plus-fill" style="color: var(--pink-accent);"></i> Add New Platform User
                </h3>
                <button type="button" onclick="closeModal('modalAddUser')" style="background: none; border: none; color: #aaa; font-size: 20px; cursor: pointer;">&times;</button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Full Name *</label>
                        <input type="text" name="name" placeholder="John Doe" class="form-control-custom" required>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Username *</label>
                        <input type="text" name="username" placeholder="johndoe47" class="form-control-custom" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Email Address *</label>
                        <input type="email" name="email" placeholder="john@example.com" class="form-control-custom" required>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Phone Number</label>
                        <input type="text" name="phone" placeholder="+123456789" class="form-control-custom">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                    <div class="form-group-custom">
                        <label class="form-label-custom">Password (Min 6 chars) *</label>
                        <input type="password" name="password" placeholder="••••••••" class="form-control-custom" required>
                    </div>

                    <div class="form-group-custom">
                        <label class="form-label-custom">Account Role *</label>
                        <select name="role" class="form-control-custom" required>
                            <option value="buyer">Buyer</option>
                            <option value="creator">Creator</option>
                            <option value="seller">Seller</option>
                            <option value="moderator">Moderator</option>
                            <option value="admin">Super Admin</option>
                            <option value="dispute_manager">Dispute Manager</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Initial Coin Balance</label>
                    <input type="number" name="coin_balance" value="0" min="0" class="form-control-custom">
                </div>

                <div style="display: flex; gap: 20px; margin: 16px 0; background: #161725; padding: 12px; border-radius: 8px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #fff; cursor: pointer;">
                        <input type="checkbox" name="is_vip" value="1"> VIP Member
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #fff; cursor: pointer;">
                        <input type="checkbox" name="is_verified" value="1"> Verified Badge
                    </label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" onclick="closeModal('modalAddUser')" class="action-btn-sm" style="padding: 10px 18px;">Cancel</button>
                    <button type="submit" style="background: var(--pink-accent); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS FOR MODAL CONTROL -->
    <script>
        function openModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('show');
            }
        }

        function closeModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Close when clicking on backdrop
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop-custom')) {
                e.target.classList.remove('show');
            }
        });
    </script>

@endsection
