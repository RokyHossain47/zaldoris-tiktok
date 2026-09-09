<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('role')->default('buyer'); // buyer, creator, seller, moderator, admin, dispute_manager
            $table->boolean('is_vip')->default(false);
            $table->string('vip_badge_tier')->nullable();
            $table->unsignedBigInteger('coin_balance')->default(0);
            $table->decimal('earnings_usd', 12, 2)->default(0.00);
            $table->boolean('is_verified')->default(false);
            $table->boolean('fast_shipper_badge')->default(false);
            $table->integer('chargeback_count')->default(0);
            $table->integer('return_strike_count')->default(0);
            $table->integer('shipping_strike_count')->default(0);
            $table->integer('overselling_strike_count')->default(0);
            $table->boolean('is_banned_from_auctions')->default(false);
            $table->boolean('is_suspended')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
