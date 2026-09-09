@extends('layouts.app')

@section('title', 'Login & Sign Up - Zaldoris')

@section('content')
<div style="max-width: 480px; margin: 40px auto;">

    <div class="zal-card" style="background: #16161f; border-radius: 20px; padding: 36px 30px;">
        
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('assets/logo.png') }}" alt="Zaldoris" style="height: 36px; margin-bottom: 12px;">
            <h1 style="font-size: 22px; font-weight: 800; color: #fff;">Welcome to Zaldoris</h1>
            <p style="color: #888; font-size: 13px; margin-top: 4px;">Join live shopping, PK battles & Whatnot auctions</p>
        </div>

        <!-- AUTH TABS -->
        <div style="display: flex; background: #121218; padding: 4px; border-radius: 12px; margin-bottom: 24px;">
            <button onclick="switchTab('login')" id="tabLoginBtn" style="flex: 1; padding: 8px; border-radius: 8px; border: none; font-weight: 800; font-size: 13px; background: #FE2C55; color: #fff; cursor: pointer;">
                Log In
            </button>
            <button onclick="switchTab('register')" id="tabRegisterBtn" style="flex: 1; padding: 8px; border-radius: 8px; border: none; font-weight: 700; font-size: 13px; background: transparent; color: #888; cursor: pointer;">
                Sign Up
            </button>
        </div>

        <!-- LOGIN FORM -->
        <form id="loginForm" action="{{ route('login') }}" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
            @csrf
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Email Address</label>
                <input type="email" name="email" value="seller@zaldoris.com" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px; outline: none;">
            </div>

            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Password</label>
                <input type="password" name="password" value="password" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px; outline: none;">
            </div>

            <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 12px; border-radius: 10px; border: none; font-size: 14px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer; margin-top: 10px;">
                Log In
            </button>

            <div style="text-align: center; margin-top: 10px; font-size: 12px; color: #777;">
                Demo Credentials: <span style="color: #25F4EE;">seller@zaldoris.com</span> / <span style="color: #25F4EE;">password</span>
            </div>
        </form>

        <!-- REGISTER FORM -->
        <form id="registerForm" action="{{ route('register') }}" method="POST" style="display: none; flex-direction: column; gap: 14px;">
            @csrf
            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Full Name</label>
                <input type="text" name="name" placeholder="John Doe" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Email Address</label>
                <input type="email" name="email" placeholder="john@example.com" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Account Type</label>
                <select name="role" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px;">
                    <option value="buyer">Buyer / Shopper</option>
                    <option value="creator">Live Creator / Influencer</option>
                    <option value="seller">Seller / Shop Merchant</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div>
                    <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Password</label>
                    <input type="password" name="password" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px;">
                </div>
                <div>
                    <label style="font-size: 12px; color: #aaa; display: block; margin-bottom: 6px;">Confirm</label>
                    <input type="password" name="password_confirmation" required style="width: 100%; background: #1f1f2a; border: 1px solid #333; color: #fff; padding: 12px 14px; border-radius: 10px; font-size: 13px;">
                </div>
            </div>

            <button type="submit" class="zal-btn-primary" style="width: 100%; padding: 12px; border-radius: 10px; border: none; font-size: 14px; font-weight: 800; background: linear-gradient(135deg, #FE2C55, #FF0055); color: #fff; cursor: pointer; margin-top: 10px;">
                Create Account
            </button>
        </form>

    </div>

</div>

@push('scripts')
<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const tabLoginBtn = document.getElementById('tabLoginBtn');
        const tabRegisterBtn = document.getElementById('tabRegisterBtn');

        if (tab === 'login') {
            loginForm.style.display = 'flex';
            registerForm.style.display = 'none';
            tabLoginBtn.style.background = '#FE2C55';
            tabLoginBtn.style.color = '#fff';
            tabRegisterBtn.style.background = 'transparent';
            tabRegisterBtn.style.color = '#888';
        } else {
            loginForm.style.display = 'none';
            registerForm.style.display = 'flex';
            tabRegisterBtn.style.background = '#FE2C55';
            tabRegisterBtn.style.color = '#fff';
            tabLoginBtn.style.background = 'transparent';
            tabLoginBtn.style.color = '#888';
        }
    }
</script>
@endpush
@endsection
