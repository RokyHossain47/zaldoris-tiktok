@extends('layouts.app')

@section('title', 'PK Battle: ' . ($battle->host1->name ?? 'Host 1') . ' vs ' . ($battle->host2->name ?? 'Host 2') . ' - Zaldoris')

@section('content')
<div style="max-width: 1300px; margin: 0 auto;">

    <!-- TOP BATTLE HEADER & SCORE BAR -->
    <div class="zal-card" style="margin-bottom: 16px; padding: 16px 20px; background: #16161f; border-radius: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 13px;">
                    ⚔️ PK BATTLE
                </span>
                <span style="color: #aaa; font-size: 13px;">5-Minute Championship Match</span>
            </div>
            
            <div style="background: #1f1f2a; padding: 6px 16px; border-radius: 20px; color: #FFB800; font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                <i class="bi bi-clock-history"></i> <span id="pkTimer">03:45</span>
            </div>
        </div>

        <!-- DYNAMIC SPLIT SCORE BAR -->
        <div style="display: flex; justify-content: space-between; font-weight: 800; font-size: 15px; margin-bottom: 6px;">
            <span style="color: #FE2C55;">{{ $battle->host1->name ?? 'Host 1' }}: <span id="host1ScoreDisplay">{{ number_format($battle->host1_score) }}</span> pts</span>
            <span style="color: #25F4EE;">{{ $battle->host2->name ?? 'Host 2' }}: <span id="host2ScoreDisplay">{{ number_format($battle->host2_score) }}</span> pts</span>
        </div>

        @php
            $total = $battle->host1_score + $battle->host2_score;
            $h1Pct = $total > 0 ? round(($battle->host1_score / $total) * 100, 1) : 50;
            $h2Pct = 100 - $h1Pct;
        @endphp

        <div style="height: 14px; background: #25F4EE; border-radius: 7px; overflow: hidden; display: flex;">
            <div id="scoreBarLeft" style="width: {{ $h1Pct }}%; background: #FE2C55; transition: width 0.4s ease;"></div>
        </div>
    </div>

    <!-- MAIN 2-CREATOR SPLIT SCREEN & CHAT -->
    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 20px; height: calc(100vh - 240px); min-height: 520px;">
        
        <!-- LEFT: 2-CREATOR SPLIT SCREEN -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; background: #000; border-radius: 16px; overflow: hidden; position: relative;">
            
            <!-- CREATOR 1 BOX -->
            <div style="position: relative; height: 100%; overflow: hidden;">
                <img src="{{ $battle->stream1->thumbnail_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600' }}" alt="Host 1" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.6); padding: 4px 10px; border-radius: 14px; color: #fff; font-size: 12px; font-weight: 700;">
                    🔴 {{ $battle->host1->name ?? 'Host 1' }}
                </div>
                <div style="position: absolute; bottom: 12px; left: 12px; right: 12px;">
                    <button onclick="openPkGiftModal({{ $battle->host1_id }})" style="width: 100%; background: #FE2C55; color: #fff; border: none; padding: 10px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(254,44,85,0.4);">
                        🎁 Boost {{ explode(' ', $battle->host1->name ?? 'Host 1')[0] }}
                    </button>
                </div>
            </div>

            <!-- VS BADGE -->
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #000; color: #FFB800; border: 2px solid #FFB800; width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 16px; z-index: 10; box-shadow: 0 0 16px rgba(255,184,0,0.6);">
                VS
            </div>

            <!-- CREATOR 2 BOX -->
            <div style="position: relative; height: 100%; overflow: hidden;">
                <img src="{{ $battle->stream2->thumbnail_url ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600' }}" alt="Host 2" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; top: 12px; right: 12px; background: rgba(0,0,0,0.6); padding: 4px 10px; border-radius: 14px; color: #fff; font-size: 12px; font-weight: 700;">
                    🔴 {{ $battle->host2->name ?? 'Host 2' }}
                </div>
                <div style="position: absolute; bottom: 12px; left: 12px; right: 12px;">
                    <button onclick="openPkGiftModal({{ $battle->host2_id }})" style="width: 100%; background: #25F4EE; color: #000; border: none; padding: 10px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; box-shadow: 0 4px 12px rgba(37,244,238,0.4);">
                        🎁 Boost {{ explode(' ', $battle->host2->name ?? 'Host 2')[0] }}
                    </button>
                </div>
            </div>

        </div>

        <!-- RIGHT: BATTLE LIVE CHAT -->
        <div style="background: #16161f; border-radius: 16px; border: 1px solid #222; display: flex; flex-direction: column; overflow: hidden;">
            <div style="padding: 14px 16px; border-bottom: 1px solid #222; font-weight: 800; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-lightning-charge-fill" style="color: #FFB800;"></i> Battle Live Feed
            </div>

            <div id="battleChatContainer" style="flex: 1; padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px;">
                <div style="background: rgba(255,184,0,0.1); border-left: 3px solid #FFB800; padding: 8px 12px; border-radius: 6px; font-size: 12px; color: #eee;">
                    Battle is LIVE! Send gifts to push the score bar in favor of your favorite creator!
                </div>
            </div>

            <div style="padding: 12px; border-top: 1px solid #222; background: #121218;">
                <form id="battleChatForm" onsubmit="sendBattleMessage(event)" style="display: flex; gap: 8px;">
                    <input type="text" id="battleChatInput" placeholder="Cheer for your creator..." required style="flex: 1; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 20px; font-size: 13px; outline: none;">
                    <button type="submit" style="background: #FE2C55; color: #fff; border: none; width: 38px; height: 38px; border-radius: 50%; cursor: pointer;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- PK GIFT MODAL -->
<div id="pkGiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #16161f; border: 1px solid #333; border-radius: 20px; max-width: 460px; width: 90%; padding: 24px; color: #fff;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div style="font-size: 16px; font-weight: 800;">Select Gift to Boost Creator</div>
            <button onclick="closePkGiftModal()" style="background: none; border: none; color: #888; font-size: 20px; cursor: pointer;">&times;</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px;">
            @foreach($gifts as $gift)
                <div onclick="selectPkGift({{ $gift->id }}, {{ $gift->coin_cost }}, '{{ $gift->name }}')" class="pk-gift-btn" id="pkgift-{{ $gift->id }}" style="background: #1f1f2a; border: 2px solid transparent; border-radius: 12px; padding: 10px 4px; text-align: center; cursor: pointer;">
                    <div style="font-size: 24px;">🎁</div>
                    <div style="font-size: 11px; font-weight: 700; margin-top: 4px;">{{ $gift->name }}</div>
                    <div style="font-size: 11px; color: #FFB800; font-weight: 800;">{{ $gift->coin_cost }} <i class="bi bi-coin"></i></div>
                </div>
            @endforeach
        </div>

        <button onclick="sendPkBattleGift()" style="width: 100%; padding: 12px; border-radius: 10px; border: none; font-size: 14px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer;">
            Confirm & Send Boost Gift
        </button>
    </div>
</div>

@push('scripts')
<script>
    let targetHostId = null;
    let selectedGiftId = null;
    let selectedGiftName = '';
    const battleId = {{ $battle->id }};

    function openPkGiftModal(hostId) {
        targetHostId = hostId;
        document.getElementById('pkGiftModal').style.display = 'flex';
    }

    function closePkGiftModal() {
        document.getElementById('pkGiftModal').style.display = 'none';
    }

    function selectPkGift(id, cost, name) {
        selectedGiftId = id;
        selectedGiftName = name;
        document.querySelectorAll('.pk-gift-btn').forEach(el => el.style.borderColor = 'transparent');
        document.getElementById('pkgift-' + id).style.borderColor = '#FE2C55';
    }

    async function sendPkBattleGift() {
        if (!selectedGiftId || !targetHostId) {
            alert('Please select a gift.');
            return;
        }

        try {
            const res = await fetch(`/api/v1/pk-battles/${battleId}/send-gift`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    gift_id: selectedGiftId,
                    target_host_id: targetHostId
                })
            });

            const data = await res.json();
            if (data.success) {
                alert(`🚀 ${data.message}`);
                document.getElementById('host1ScoreDisplay').innerText = data.updated_scores.host1_score.toLocaleString();
                document.getElementById('host2ScoreDisplay').innerText = data.updated_scores.host2_score.toLocaleString();
                document.getElementById('scoreBarLeft').style.width = data.updated_scores.host1_percentage + '%';
                
                const container = document.getElementById('battleChatContainer');
                const div = document.createElement('div');
                div.style.fontSize = '13px';
                div.innerHTML = `<strong style="color: #FFB800;">Gift Alert:</strong> Sent ${selectedGiftName} boost! 🔥`;
                container.appendChild(div);
                closePkGiftModal();
            } else {
                alert(data.message || 'Error sending battle gift.');
            }
        } catch (err) {
            alert('Failed to send gift. Ensure you are logged in with sufficient coins.');
        }
    }

    function sendBattleMessage(e) {
        e.preventDefault();
        const input = document.getElementById('battleChatInput');
        const text = input.value.trim();
        if (!text) return;

        const container = document.getElementById('battleChatContainer');
        const div = document.createElement('div');
        div.style.fontSize = '13px';
        div.innerHTML = `<strong style="color: #FE2C55;">You:</strong> <span style="color: #ddd;">${text}</span>`;
        container.appendChild(div);
        input.value = '';
    }
</script>
@endpush
@endsection
