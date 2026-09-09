@extends('layouts.app')

@section('title', 'Express Checkout - Zaldoris')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 24px;">Express Checkout</h1>

    <form action="{{ route('shop.checkout.process') }}" method="POST">
        @csrf
        <input type="hidden" name="items[0][product_id]" value="1">
        <input type="hidden" name="items[0][quantity]" value="1">

        <div style="display: grid; grid-template-columns: 1fr 340px; gap: 30px;">
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- SHIPPING ADDRESS (SRS #5, #15) -->
                <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">1. Shipping Address</h3>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <label style="font-size: 12px; color: #888; display: block; margin-bottom: 4px;">Street Address</label>
                            <input type="text" name="street" value="120 Bay Street, Suite 800" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="font-size: 12px; color: #888; display: block; margin-bottom: 4px;">City</label>
                                <input type="text" name="city" value="Toronto" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #888; display: block; margin-bottom: 4px;">Province</label>
                                <input type="text" name="province" value="ON" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="font-size: 12px; color: #888; display: block; margin-bottom: 4px;">Postal Code</label>
                                <input type="text" name="postal_code" value="M5J 2R8" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="font-size: 12px; color: #888; display: block; margin-bottom: 4px;">Country</label>
                                <input type="text" name="country" value="Canada" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PAYMENT METHOD (SRS #15) -->
                <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 24px;">
                    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">2. Payment Method</h3>
                    <div style="display: flex; gap: 12px;">
                        <label style="flex: 1; background: #1f1f2a; border: 2px solid #FE2C55; padding: 12px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700;">
                            <input type="radio" name="payment_method" value="stripe_card" checked style="accent-color: #FE2C55;">
                            <span><i class="bi bi-credit-card-2-front"></i> Credit / Debit Card</span>
                        </label>
                        <label style="flex: 1; background: #1f1f2a; border: 2px solid transparent; padding: 12px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700;">
                            <input type="radio" name="payment_method" value="apple_pay" style="accent-color: #FE2C55;">
                            <span><i class="bi bi-apple"></i> Apple / Google Pay</span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- ORDER TOTAL & SUBMIT -->
            <div class="zal-card" style="background: #16161f; border-radius: 16px; padding: 24px; height: fit-content;">
                <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Summary</h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #888;">Subtotal</span>
                        <strong style="color: #fff;">$380.00</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #888;">Shipping</span>
                        <strong style="color: #fff;">$10.00</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #888;">Insurance (> $50)</span>
                        <strong style="color: #25F4EE;">$1.90</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #888;">13% HST</span>
                        <strong style="color: #fff;">$50.95</strong>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 900; margin-bottom: 20px;">
                    <span>Total Due</span>
                    <span style="color: #25F4EE;">$442.85</span>
                </div>

                <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 14px; border-radius: 10px; border: none; font-weight: 800; font-size: 14px; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer;">
                    Complete Order
                </button>
            </div>

        </div>
    </form>

</div>
@endsection
