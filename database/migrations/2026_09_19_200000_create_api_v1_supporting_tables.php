<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. User Addresses
        if (!Schema::hasTable('user_addresses')) {
            Schema::create('user_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('recipient_name');
                $table->string('phone')->nullable();
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('region')->nullable();
                $table->string('postal_code');
                $table->string('country')->default('CA');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // 2. User Preferences
        if (!Schema::hasTable('user_preferences')) {
            Schema::create('user_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                $table->string('language')->default('en');
                $table->string('currency')->default('CAD');
                $table->string('theme')->default('dark');
                $table->json('notification_settings')->nullable();
                $table->text('bio')->nullable();
                $table->string('gender')->nullable();
                $table->date('birth_date')->nullable();
                $table->timestamps();
            });
        }

        // 3. Auth Challenges (OTP verification for login, register, contact change, payment)
        if (!Schema::hasTable('auth_challenges')) {
            Schema::create('auth_challenges', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('challenge_type'); // 'register', 'login', 'contact_change', 'password_reset', 'payment'
                $table->string('destination'); // email or phone
                $table->string('code');
                $table->string('token', 64)->unique();
                $table->json('payload')->nullable();
                $table->timestamp('expires_at');
                $table->timestamp('resend_available_at')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Uploads
        if (!Schema::hasTable('uploads')) {
            Schema::create('uploads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('filename');
                $table->string('original_name')->nullable();
                $table->string('path');
                $table->string('url');
                $table->string('permitted_url')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size_bytes')->default(0);
                $table->string('status')->default('pending'); // 'pending', 'completed', 'failed'
                $table->timestamps();
            });
        }

        // 5. Search Histories
        if (!Schema::hasTable('search_histories')) {
            Schema::create('search_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('query');
                $table->timestamps();
            });
        }

        // 6. Wishlists
        if (!Schema::hasTable('wishlists')) {
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('target_type'); // 'product', 'auction'
                $table->unsignedBigInteger('target_id');
                $table->json('preferred_variant')->nullable();
                $table->integer('preferred_quantity')->default(1);
                $table->timestamps();

                $table->unique(['user_id', 'target_type', 'target_id']);
            });
        }

        // 7. Cart Items (Persistent API Cart)
        if (!Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->string('selected_color')->nullable();
                $table->string('selected_size')->nullable();
                $table->integer('quantity')->default(1);
                $table->decimal('unit_price', 10, 2)->default(0.00);
                $table->json('options')->nullable();
                $table->timestamps();
            });
        }

        // 8. Payment Methods
        if (!Schema::hasTable('payment_methods')) {
            Schema::create('payment_methods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('provider')->default('stripe');
                $table->string('token')->nullable();
                $table->string('type')->default('card'); // 'card', 'paypal', 'apple_pay', 'google_pay'
                $table->string('brand')->nullable(); // 'visa', 'mastercard', 'amex'
                $table->string('last_four', 4)->nullable();
                $table->string('expiry_month', 2)->nullable();
                $table->string('expiry_year', 4)->nullable();
                $table->boolean('is_default')->default(false);
                $table->json('supported_contexts')->nullable();
                $table->timestamps();
            });
        }

        // 9. Checkout Quotes
        if (!Schema::hasTable('checkout_quotes')) {
            Schema::create('checkout_quotes', function (Blueprint $table) {
                $table->id();
                $table->string('quote_id', 40)->unique();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('currency', 3)->default('CAD');
                $table->json('items');
                $table->json('seller_groups')->nullable();
                $table->json('shipping_address')->nullable();
                $table->string('shipping_method')->default('standard');
                $table->string('promo_code')->nullable();
                $table->decimal('item_subtotal', 10, 2)->default(0.00);
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->decimal('platform_fee', 10, 2)->default(0.00);
                $table->decimal('processing_fee', 10, 2)->default(0.00);
                $table->decimal('shipping_fee', 10, 2)->default(0.00);
                $table->decimal('tax_amount', 10, 2)->default(0.00);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                $table->string('status')->default('active'); // 'active', 'converted', 'expired'
                $table->timestamp('expires_at');
                $table->timestamps();
            });
        }

        // 10. Payments
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
                $table->string('quote_id')->nullable();
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->string('currency', 3)->default('CAD');
                $table->string('payment_method')->default('card');
                $table->string('provider')->default('stripe');
                $table->string('transaction_reference')->nullable();
                $table->string('status')->default('succeeded'); // 'pending', 'authentication_required', 'succeeded', 'failed', 'cancelled'
                $table->string('challenge_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // 11. Order Returns & Refunds
        if (!Schema::hasTable('order_returns')) {
            Schema::create('order_returns', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('reason_code'); // 'defective', 'wrong_item', 'not_as_described', 'buyer_remorse'
                $table->text('description')->nullable();
                $table->json('evidence_asset_ids')->nullable();
                $table->decimal('refund_amount', 10, 2)->default(0.00);
                $table->string('status')->default('pending_review'); // 'pending_review', 'approved', 'rejected', 'items_received', 'refunded'
                $table->text('instructions')->nullable();
                $table->string('return_tracking_number')->nullable();
                $table->timestamp('return_deadline')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('order_return_id')->nullable()->constrained('order_returns')->onDelete('set null');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->string('currency', 3)->default('CAD');
                $table->string('status')->default('completed'); // 'pending', 'completed', 'failed'
                $table->string('reason')->nullable();
                $table->string('original_payment_method')->default('stripe_card');
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }

        // 12. Conversations & Direct Chat Messages
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_one_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('user_two_id')->constrained('users')->onDelete('cascade');
                $table->text('last_message')->nullable();
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                $table->unique(['user_one_id', 'user_two_id']);
            });
        }

        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->string('client_message_id')->nullable();
                $table->text('message');
                $table->json('attachments')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // 13. Gamification & Reward Tiers
        if (!Schema::hasTable('reward_tiers')) {
            Schema::create('reward_tiers', function (Blueprint $table) {
                $table->id();
                $table->integer('tier_level')->default(1);
                $table->string('name'); // Bronze, Silver, Gold, Platinum, Diamond
                $table->integer('min_xp')->default(0);
                $table->integer('max_xp')->default(1000);
                $table->json('benefits')->nullable();
                $table->string('icon_url')->nullable();
                $table->string('badge_color')->default('#00F0C8');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_rewards')) {
            Schema::create('user_rewards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                $table->foreignId('current_tier_id')->nullable()->constrained('reward_tiers')->onDelete('set null');
                $table->integer('xp_points')->default(0);
                $table->integer('lifetime_xp')->default(0);
                $table->integer('streak_days')->default(1);
                $table->timestamps();
            });
        }

        // 14. User Devices (Push Notification Tokens)
        if (!Schema::hasTable('user_devices')) {
            Schema::create('user_devices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('installation_id')->unique();
                $table->string('push_token');
                $table->string('device_type')->default('android'); // 'ios', 'android', 'web'
                $table->string('os_version')->nullable();
                $table->string('app_version')->nullable();
                $table->timestamps();
            });
        }

        // 15. Moderation Reports & Blocked Users
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
                $table->string('target_type'); // 'user', 'stream', 'comment', 'message'
                $table->unsignedBigInteger('target_id');
                $table->string('reason');
                $table->text('details')->nullable();
                $table->string('status')->default('pending'); // 'pending', 'resolved', 'dismissed'
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blocked_users')) {
            Schema::create('blocked_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'blocked_user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_rewards');
        Schema::dropIfExists('reward_tiers');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('checkout_quotes');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('search_histories');
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('auth_challenges');
        Schema::dropIfExists('user_preferences');
        Schema::dropIfExists('user_addresses');
    }
};
