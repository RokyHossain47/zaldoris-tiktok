@extends('layouts.app')

@section('title', 'Verify Phone OTP - Zaldoris')

@section('content')
<div style="max-width: 440px; margin: 40px auto; text-align: center;">

    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 36px 28px;">
        
        <div style="font-size: 48px; margin-bottom: 16px;">📱</div>
        <h1 style="font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 8px;">Phone Verification</h1>
        <p style="color: #888; font-size: 13px; margin-bottom: 24px;">
            To ensure fair auctions and prevent fake bot accounts (SRS #3), please enter the 6-digit OTP code sent to your phone.
        </p>

        <form action="{{ route('auth.otp.verify') }}" method="POST">
            @csrf
            <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 24px;">
                <input type="text" name="otp_code" maxlength="6" value="123456" required style="letter-spacing: 12px; font-size: 24px; font-weight: 900; text-align: center; width: 220px; background: #1f1f2a; border: 2px solid #FE2C55; color: #25F4EE; padding: 10px; border-radius: 10px; outline: none;">
            </div>

            <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 12px; border-radius: 10px; border: none; font-size: 14px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer;">
                Verify & Continue
            </button>
        </form>

        <p style="font-size: 12px; color: #666; margin-top: 20px;">
            Demo OTP code: <strong style="color: #25F4EE;">123456</strong>
        </p>

    </div>

</div>
@endsection
