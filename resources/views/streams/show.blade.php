@extends('layouts.app')

@section('title', $stream->title . ' - Zaldoris Live')

@section('content')
<div class="live-room-container" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 380px; gap: 20px; height: calc(100vh - 120px); min-height: 600px;">
    
    <!-- LEFT: VIDEO STREAM PLAYER -->
    <div class="video-stream-box" style="position: relative; background: #000; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        
        <!-- Live Video Element -->
        <div id="agora-player" style="width: 100%; height: 100%; position: relative;">
            <img src="{{ $stream->thumbnail_url }}" alt="Live Feed" style="width: 100%; height: 100%; object-fit: cover; filter: brightness(0.95);">
            
            <!-- Agora RTC Overlay / Simulation Badge -->
            <div style="position: absolute; top: 16px; left: 16px; display: flex; align-items: center; gap: 10px; z-index: 10;">
                <div style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px;">
                    <img src="{{ $stream->host->avatar_url }}" alt="{{ $stream->host->name }}" style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #FE2C55;">
                    <div>
                        <div style="font-weight: 700; font-size: 13px; color: #fff;">{{ $stream->host->name }}</div>
                        <div style="font-size: 11px; color: #aaa;">{{ number_format($stream->host->creatorProfile->follower_count ?? 1500) }} followers</div>
                    </div>
                    <button style="background: #FE2C55; color: #fff; border: none; border-radius: 14px; padding: 4px 10px; font-size: 11px; font-weight: 700; margin-left: 6px; cursor: pointer;">
                        + Follow
                    </button>
                </div>
                
                <div style="background: #FE2C55; color: #fff; padding: 4px 10px; border-radius: 14px; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 4px;">
                    <span class="live-pulse-dot"></span> LIVE
                </div>
            </div>

            <!-- Top Right: Viewer Count & Failover health -->
            <div style="position: absolute; top: 16px; right: 16px; display: flex; gap: 8px; z-index: 10;">
                <div style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #fff; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-eye-fill" style="color: #25F4EE;"></i> <span id="viewerCountDisplay">{{ number_format($stream->viewer_count) }}</span>
                </div>
                <div style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); color: #25F4EE; padding: 6px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;" title="Agora RTC Low Latency Stream">
                    <i class="bi bi-broadcast"></i> RTC HD
                </div>
            </div>

            <!-- FLOATING PINNED PRODUCT OVERLAY (SRS Page 2) -->
            @if($pinnedProduct)
                <div class="live-pinned-product" style="position: absolute; bottom: 80px; left: 16px; background: rgba(22, 22, 31, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; gap: 12px; max-width: 340px; z-index: 15; box-shadow: 0 8px 24px rgba(0,0,0,0.4);">
                    <img src="{{ $pinnedProduct->primary_image }}" alt="{{ $pinnedProduct->title }}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 10px; color: #25F4EE; font-weight: 800; text-transform: uppercase;">🔥 PINNED PRODUCT</div>
                        <div style="font-size: 12px; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $pinnedProduct->title }}</div>
                        <div style="color: #FE2C55; font-weight: 800; font-size: 13px;">${{ number_format($pinnedProduct->price, 2) }}</div>
                    </div>
                    <a href="{{ route('shop.product', $pinnedProduct->id) }}" class="zal-btn-primary" style="padding: 6px 12px; font-size: 12px; border-radius: 8px; text-decoration: none; white-space: nowrap; background: #FE2C55; color: #fff; font-weight: 700;">
                        Buy Now
                    </a>
                </div>
            @endif

            <!-- Stream Bottom Controls (Gifting, Like particle) -->
            <div style="position: absolute; bottom: 16px; left: 16px; right: 16px; display: flex; justify-content: space-between; align-items: center; z-index: 10;">
                <div style="display: flex; gap: 8px;">
                    <button id="openGiftBtn" style="background: linear-gradient(135deg, #FFB800, #FF8A00); color: #000; border: none; padding: 8px 16px; border-radius: 20px; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(255,184,0,0.4);">
                        🎁 Send Gift
                    </button>
                    @if($stream->stream_type === 'live_shopping')
                        <a href="{{ route('shop.index') }}" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); color: #fff; padding: 8px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                            🛍️ Showcase ({{ $stream->products->count() }})
                        </a>
                    @endif
                </div>

                <div style="display: flex; gap: 10px;">
                    <button onclick="triggerFloatingHeart()" style="width: 44px; height: 44px; border-radius: 50%; background: rgba(254,44,85,0.3); border: 1px solid #FE2C55; color: #FE2C55; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
                        ❤️
                    </button>
                    <button onclick="shareStream()" style="width: 44px; height: 44px; border-radius: 50%; background: rgba(0,0,0,0.6); border: 1px solid rgba(255,255,255,0.2); color: #fff; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-share-fill"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- RIGHT: LIVE CHAT & GIFTS FEED -->
    <div class="live-chat-panel" style="background: #16161f; border-radius: 16px; border: 1px solid #222; display: flex; flex-direction: column; overflow: hidden;">
        
        <div style="padding: 14px 16px; border-bottom: 1px solid #222; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-chat-dots-fill" style="color: #FE2C55;"></i> Live Chat
            </div>
            <div style="font-size: 11px; color: #25F4EE; background: rgba(37,244,238,0.1); padding: 2px 8px; border-radius: 10px;">
                🛡️ AI Moderated
            </div>
        </div>

        <!-- Chat messages stream -->
        <div id="chatMessagesContainer" style="flex: 1; padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: rgba(254,44,85,0.1); border-left: 3px solid #FE2C55; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: #eee;">
                Welcome to the live room! Be respectful and follow community guidelines.
            </div>

            @foreach($stream->messages as $msg)
                <div style="font-size: 13px; line-height: 1.4;">
                    <strong style="color: {{ $msg->is_bot ? '#25F4EE' : '#FE2C55' }};">{{ $msg->username_display ?? ($msg->user->name ?? 'Viewer') }}:</strong>
                    <span style="color: #ddd; margin-left: 4px;">{{ $msg->message }}</span>
                </div>
            @endforeach
        </div>

        <!-- Chat Input Form -->
        <div style="padding: 12px; border-top: 1px solid #222; background: #121218;">
            <form id="chatForm" onsubmit="sendChatMessage(event)" style="display: flex; gap: 8px;">
                <input type="text" id="chatInput" placeholder="Send a message..." required style="flex: 1; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 20px; font-size: 13px; outline: none;">
                <button type="submit" style="background: #FE2C55; color: #fff; border: none; width: 38px; height: 38px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>

    </div>

</div>

<!-- GIFTS POPUP MODAL (8 Core SRS Gifts) -->
<div id="giftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #16161f; border: 1px solid #333; border-radius: 20px; max-width: 480px; width: 90%; padding: 24px; color: #fff; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-gift-fill" style="color: #FFB800;"></i> Send a Gift (60% to Creator)
            </div>
            <button onclick="closeGiftModal()" style="background: none; border: none; color: #888; font-size: 20px; cursor: pointer;">&times;</button>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; background: #121218; padding: 10px 14px; border-radius: 12px; margin-bottom: 16px;">
            <span style="font-size: 13px; color: #aaa;">Your Coin Balance:</span>
            <span style="font-weight: 800; color: #FFB800; font-size: 15px;">
                <i class="bi bi-coin"></i> <span id="modalCoinBalance">{{ auth()->check() ? auth()->user()->coin_balance : 100 }}</span>
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px;">
            @foreach($gifts as $gift)
                <div onclick="selectGift({{ $gift->id }}, '{{ $gift->name }}', {{ $gift->coin_cost }})" class="gift-select-item" id="gift-item-{{ $gift->id }}" style="background: #1f1f2a; border: 2px solid transparent; border-radius: 12px; padding: 10px 4px; text-align: center; cursor: pointer; transition: all 0.2s;">
                    <div style="font-size: 26px;">
                        @if($gift->animation_type === 'heart') 💖
                        @elseif($gift->animation_type === 'sparkle') ⭐
                        @elseif($gift->animation_type === 'burst') ⚡
                        @elseif($gift->animation_type === 'wave') 🌊
                        @elseif($gift->animation_type === 'crown') 👑
                        @elseif($gift->animation_type === 'dragon') 🐉
                        @elseif($gift->animation_type === 'cosmic') 🌌
                        @else 🎁
                        @endif
                    </div>
                    <div style="font-size: 11px; font-weight: 700; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $gift->name }}</div>
                    <div style="font-size: 11px; color: #FFB800; font-weight: 800; margin-top: 2px;">{{ $gift->coin_cost }} <i class="bi bi-coin"></i></div>
                </div>
            @endforeach
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('wallet.coins') }}" class="zal-btn-secondary" style="flex: 1; text-align: center; padding: 10px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; background: #222; color: #fff;">
                Top Up Coins
            </a>
            <button id="confirmSendGiftBtn" onclick="confirmSendGift()" style="flex: 1; padding: 10px; border-radius: 10px; border: none; font-size: 13px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer;">
                Send Selected Gift
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedGiftId = null;
    let selectedGiftName = '';
    let selectedGiftCost = 0;
    const streamId = {{ $stream->id }};

    document.getElementById('openGiftBtn').onclick = () => {
        document.getElementById('giftModal').style.display = 'flex';
    };

    function closeGiftModal() {
        document.getElementById('giftModal').style.display = 'none';
    }

    function selectGift(id, name, cost) {
        selectedGiftId = id;
        selectedGiftName = name;
        selectedGiftCost = cost;
        document.querySelectorAll('.gift-select-item').forEach(el => el.style.borderColor = 'transparent');
        document.getElementById('gift-item-' + id).style.borderColor = '#FE2C55';
    }

    async function confirmSendGift() {
        if (!selectedGiftId) {
            alert('Please select a gift first.');
            return;
        }

        try {
            const res = await fetch(`/api/v1/streams/${streamId}/send-gift`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ gift_id: selectedGiftId })
            });

            const data = await res.json();
            if (data.success) {
                alert(`🎉 ${data.message}`);
                document.getElementById('modalCoinBalance').innerText = data.new_coin_balance;
                appendChatMessage('System', `Sent ${selectedGiftName}! 🎁`, '#FFB800');
                closeGiftModal();
            } else {
                alert(data.message || 'Could not send gift.');
            }
        } catch (e) {
            alert('Failed to send gift. Please make sure you are logged in and have enough coins.');
        }
    }

    async function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (!text) return;

        try {
            const res = await fetch(`/api/v1/streams/${streamId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            if (data.success) {
                appendChatMessage('You', text, '#FE2C55');
                input.value = '';
            } else {
                alert(data.message || 'Message blocked by moderation.');
            }
        } catch (err) {
            appendChatMessage('You', text, '#FE2C55');
            input.value = '';
        }
    }

    function appendChatMessage(user, msg, color) {
        const container = document.getElementById('chatMessagesContainer');
        const div = document.createElement('div');
        div.style.fontSize = '13px';
        div.style.lineHeight = '1.4';
        div.innerHTML = `<strong style="color: ${color};">${user}:</strong> <span style="color: #ddd; margin-left: 4px;">${msg}</span>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function triggerFloatingHeart() {
        appendChatMessage('System', '❤️ Sent a heart!', '#FE2C55');
    }

    function shareStream() {
        navigator.clipboard.writeText(window.location.href);
        alert('Live stream link copied to clipboard!');
    }
</script>
@endpush
@endsection
