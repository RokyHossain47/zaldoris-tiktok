<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('business_name')->nullable();
            $table->string('business_number')->nullable(); // Government business number
            $table->string('id_proof_url')->nullable(); // Government ID verification
            $table->string('sin_last4', 4)->nullable(); // SIN verification
            $table->timestamp('sin_verified_at')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->boolean('live_selling_active')->default(false); // Mandatory live selling activity check
            $table->decimal('on_time_dispatch_rate', 5, 2)->default(100.00); // 48h dispatch rate for Fast Shipper badge
            $table->integer('total_orders')->default(0);
            $table->integer('total_dispatches')->default(0);
            $table->string('audit_status')->default('normal'); // normal, flagged, under_review
            $table->boolean('packing_video_required')->default(true);
            $table->timestamps();
        });

        Schema::create('creator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->integer('follower_count')->default(0);
            $table->integer('following_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->decimal('subscription_fee', 8, 2)->default(7.99); // Monthly creator subscription $7.99
            $table->decimal('revenue_share_rate', 5, 2)->default(60.00); // 60% creator revenue share on gifts
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_profiles');
        Schema::dropIfExists('seller_profiles');
    }
};
