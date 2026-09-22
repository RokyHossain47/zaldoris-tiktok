<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('ad_type'); // pre_roll, banner, host, interactive, sponsored_gift, sponsored_leaderboard
            $table->string('media_url')->nullable();
            $table->string('link_url')->nullable();
            $table->integer('interval_minutes')->default(15); // Interactive ads every 15-20 min (SRS Page 5)
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type'); // chargeback, return_abuse, shill_bidding, wash_trading, overselling, counterfeit
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->string('status')->default('pending'); // pending, investigating, resolved, action_taken
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('ai_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('content_type'); // stream_chat, product_title, review, stream_video
            $table->unsignedBigInteger('content_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('flagged_content');
            $table->string('violation_type'); // nudity, hate_speech, scam, prohibited_ingredient
            $table->decimal('confidence_score', 5, 2)->default(0.95);
            $table->string('action_taken')->default('blocked'); // blocked, warning, shadowbanned
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_moderation_logs');
        Schema::dropIfExists('fraud_alerts');
        Schema::dropIfExists('advertisements');
    }
};
