<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->decimal('starting_bid', 10, 2)->default(1.00);
            $table->decimal('reserve_price', 10, 2)->nullable();
            $table->decimal('current_bid', 10, 2)->default(1.00);
            $table->foreignId('highest_bidder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('min_bid_step', 10, 2)->default(5.00);
            $table->string('status')->default('active'); // upcoming, active, sold, failed, cancelled
            $table->integer('fail_count')->default(0); // automatic removal after 3 failed auctions (SRS Page 3)
            $table->boolean('is_blurred')->default(false); // listing blur when auction expires
            $table->timestamp('relist_available_at')->nullable(); // 1-month restriction before re-listing
            $table->boolean('seller_approved_relist')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('ip_address')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->boolean('is_flagged_shill')->default(false); // flagged for duplicate IP/device (SRS #3)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
        Schema::dropIfExists('auctions');
    }
};
