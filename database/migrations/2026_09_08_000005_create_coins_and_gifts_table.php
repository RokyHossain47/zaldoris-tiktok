<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coin_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('coins');
            $table->decimal('price_usd', 8, 2);
            $table->unsignedBigInteger('bonus_coins')->default(0);
            $table->string('badge_tier')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // purchase, gift_sent, gift_received, subscription, bonus
            $table->bigInteger('amount_coins');
            $table->decimal('amount_usd', 10, 2)->nullable();
            $table->decimal('hst_tax_usd', 8, 2)->default(0.00); // 13% HST tax on transactions (SRS Page 5)
            $table->string('payment_method')->default('stripe'); // stripe, apple_pay, google_pay, internal
            $table->string('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Glow Heart, Star Wink, Energy Shot, Sonic Bloom, Golden Wave, Royal Throne, Dragon Ascend, Cosmic Empire
            $table->string('slug')->unique();
            $table->unsignedBigInteger('coin_cost');
            $table->string('icon_url')->nullable();
            $table->string('animation_type')->default('sparkle');
            $table->string('tier_ladder')->default('standard'); // standard, vip, exclusive
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('gift_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('gift_id')->constrained('gifts')->cascadeOnDelete();
            $table->unsignedBigInteger('coin_amount');
            $table->decimal('creator_earning_usd', 10, 2); // 60% creator share after fees (SRS Page 4)
            $table->decimal('platform_commission_usd', 10, 2); // 40% platform revenue
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_transactions');
        Schema::dropIfExists('gifts');
        Schema::dropIfExists('coin_transactions');
        Schema::dropIfExists('coin_packages');
    }
};
