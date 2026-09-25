@extends('layouts.admin')

@section('title', 'Shipping Methods - Super Admin')

@section('content')
<div style="max-width: 960px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 800; color: #fff; margin-bottom: 4px;">Shipping Methods</h1>
            <p style="font-size: 14px; color: var(--text-muted);">Configure available shipping tiers, flat rates, express couriers, and delivery timeframes for customer checkout.</p>
        </div>
        <button type="button" onclick="addShippingMethodRow()" style="background: rgba(0, 240, 200, 0.15); border: 1px solid var(--cyan-accent); color: var(--cyan-accent); padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="bi bi-plus-circle-fill"></i> + Add Shipping Method
        </button>
    </div>

    <!-- SETTINGS SUB-NAV TABS -->
    <div style="display: flex; gap: 8px; margin-bottom: 24px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; flex-wrap: wrap;">
        <a href="{{ route('admin.settings.general') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-sliders"></i> General
        </a>
        <a href="{{ route('admin.settings.seo') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-search-heart"></i> SEO & Metadata
        </a>
        <a href="{{ route('admin.settings.system') }}" style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;">
            <i class="bi bi-cpu"></i> System & Localization
        </a>
        <a href="{{ route('admin.settings.shipping') }}" style="background: var(--pink-accent); color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
            <i class="bi bi-truck"></i> Shipping Methods
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(0, 240, 200, 0.15); border: 1px solid var(--cyan-accent); color: var(--cyan-accent); padding: 14px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 16px; padding: 32px; box-shadow: 0 10px 30px rgba(0,0,0,0.4);">
        <form action="{{ route('admin.settings.shipping.update') }}" method="POST" id="shippingSettingsForm">
            @csrf

            <div id="shippingMethodsContainer" style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px;">
                <!-- Dynamically rendered rows -->
            </div>

            <div style="display: flex; gap: 16px; align-items: center;">
                <button type="submit" style="background: linear-gradient(135deg, var(--cyan-accent, #00F0C8), var(--cyan-hover, #00D8B4)); color: #090D10; border: none; padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 15px; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(0, 240, 200, 0.3);">
                    <i class="bi bi-check-circle-fill"></i> Save Shipping Methods
                </button>
                <button type="button" onclick="addShippingMethodRow()" style="background: rgba(255, 255, 255, 0.06); color: #fff; border: 1px solid var(--border-color); padding: 14px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer;">
                    + Add Another Method
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let methodIndex = 0;
    const initialMethods = @json($methods ?? []);
    const currencySymbol = '{{ setting('currency_symbol', '$') }}';

    function addShippingMethodRow(data = null) {
        const idx = methodIndex++;
        const container = document.getElementById('shippingMethodsContainer');
        
        const id = data && data.id ? data.id : `method_${Date.now()}_${idx}`;
        const name = data && data.name ? data.name : '';
        const cost = data && data.cost !== undefined ? data.cost : 0.00;
        const deliveryTime = data && data.delivery_time ? data.delivery_time : '3 - 5 Business Days';
        const description = data && data.description ? data.description : '';
        const isActive = data && data.is_active !== undefined ? data.is_active : true;
        const isDefault = data && data.is_default !== undefined ? data.is_default : false;

        const rowCard = document.createElement('div');
        rowCard.id = `shipping_card_${idx}`;
        rowCard.style = 'background: var(--bg-card-inner); border: 1px solid var(--border-color); border-radius: 14px; padding: 22px; position: relative; transition: border-color 0.2s;';

        rowCard.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: rgba(0, 240, 200, 0.15); color: var(--cyan-accent); font-weight: 800; font-size: 12px; width: 26px; height: 26px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-truck"></i>
                    </span>
                    <input type="text" name="methods[${idx}][name]" value="${escapeHtml(name)}" placeholder="Shipping Method Name (e.g. Express Courier)" required style="background: transparent; border: none; border-bottom: 2px solid rgba(255,255,255,0.15); color: #fff; font-size: 16px; font-weight: 800; padding: 4px 6px; outline: none; min-width: 260px;">
                    <input type="hidden" name="methods[${idx}][id]" value="${escapeHtml(id)}">
                </div>

                <div style="display: flex; align-items: center; gap: 14px;">
                    <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #CBD5E1; cursor: pointer;">
                        <input type="checkbox" name="methods[${idx}][is_active]" value="1" ${isActive ? 'checked' : ''} style="width: 16px; height: 16px; accent-color: var(--cyan-accent);">
                        <span>Active</span>
                    </label>

                    <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #CBD5E1; cursor: pointer;">
                        <input type="radio" name="default_selection_radio" value="${idx}" ${isDefault ? 'checked' : ''} onchange="handleDefaultChange(${idx})" style="width: 16px; height: 16px; accent-color: var(--pink-accent);">
                        <span>Default Option</span>
                    </label>
                    <input type="hidden" name="methods[${idx}][is_default]" id="is_default_input_${idx}" value="${isDefault ? '1' : '0'}">

                    <button type="button" onclick="removeShippingRow('shipping_card_${idx}')" style="background: rgba(254, 44, 85, 0.15); border: 1px solid rgba(254, 44, 85, 0.3); color: #FE2C55; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="Remove this shipping method">
                        <i class="bi bi-trash-fill"></i>
                    </button>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Shipping Cost (${currencySymbol})</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <span style="position: absolute; left: 12px; color: var(--text-muted); font-weight: 700; font-size: 13px;">${currencySymbol}</span>
                        <input type="number" step="0.01" min="0" name="methods[${idx}][cost]" value="${parseFloat(cost).toFixed(2)}" required placeholder="0.00" style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px 10px 28px; border-radius: 8px; font-size: 14px; font-weight: 700;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Estimated Delivery Timeframe</label>
                    <input type="text" name="methods[${idx}][delivery_time]" value="${escapeHtml(deliveryTime)}" placeholder="e.g. 1 - 2 Business Days / Next-Day" required style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px;">Description / Carrier Details (Optional)</label>
                <input type="text" name="methods[${idx}][description]" value="${escapeHtml(description)}" placeholder="e.g. Tracked doorstep delivery with SMS notifications and insurance." style="width: 100%; background: #0c0d14; border: 1px solid var(--border-color); color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
            </div>
        `;

        container.appendChild(rowCard);
    }

    function removeShippingRow(rowId) {
        const el = document.getElementById(rowId);
        if (el) {
            el.remove();
        }
    }

    function handleDefaultChange(selectedIdx) {
        document.querySelectorAll('[id^="is_default_input_"]').forEach(input => {
            input.value = '0';
        });
        const activeInput = document.getElementById(`is_default_input_${selectedIdx}`);
        if (activeInput) {
            activeInput.value = '1';
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (initialMethods && initialMethods.length > 0) {
            initialMethods.forEach(m => addShippingMethodRow(m));
        } else {
            addShippingMethodRow({
                name: 'Standard Ground Delivery',
                cost: 0.00,
                delivery_time: '3 - 5 Business Days',
                description: 'Reliable tracked doorstep parcel delivery.',
                is_active: true,
                is_default: true,
            });
            addShippingMethodRow({
                name: 'Express Priority Courier',
                cost: 15.00,
                delivery_time: '1 - 2 Business Days',
                description: 'Fast air dispatch with real-time tracking.',
                is_active: true,
                is_default: false,
            });
        }
    });
</script>
@endsection
