<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('auction_id')->nullable()->constrained('auctions')->nullOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('hst_tax', 10, 2)->default(0.00); // 13% HST
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->decimal('insurance_fee', 10, 2)->default(0.00); // Mandatory insurance > $50 (SRS #8)
            $table->decimal('platform_commission', 10, 2)->default(0.00); // 7% product sales commission
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, escrow_held, released_to_seller, refunded
            $table->string('payment_method')->default('stripe');
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->json('shipping_address')->nullable();
            $table->string('status')->default('pending'); // pending, packing, shipped, delivered, completed, cancelled, disputed
            $table->timestamp('dispatch_deadline')->nullable(); // 48h dispatch requirement
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->default('easypost');
            $table->string('packing_video_url')->nullable(); // 30-sec packing video (SRS #2)
            $table->boolean('requires_signature')->default(false); // Orders > $150 (SRS #8)
            $table->boolean('is_store_credit_refund')->default(false); // 24-hr impulse cancel store credit (SRS #6)
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_title');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        Schema::create('creator_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('monthly_price', 8, 2)->default(7.99); // $7.99 monthly (SRS Page 4)
            $table->string('status')->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->text('description')->nullable();
            $table->string('received_item_photo_url')->nullable(); // Photo of received item required (SRS #2)
            $table->string('status')->default('open'); // open, under_review, resolved_refund, resolved_credit, rejected
            $table->timestamp('response_due_at')->nullable(); // Hard 4-hour SLA (SRS #10)
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->integer('rating')->default(5); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('verified_purchase')->default(true); // Gated to verified buyers 3 days post delivery (SRS #9)
            $table->boolean('is_flagged_anomaly')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('creator_subscriptions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
