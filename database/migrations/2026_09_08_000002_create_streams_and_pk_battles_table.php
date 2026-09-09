<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general');
            $table->string('stream_type')->default('standard'); // standard, live_shopping, live_auction, pk_battle
            $table->string('agora_channel')->unique();
            $table->text('agora_token')->nullable();
            $table->string('stream_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('is_live')->default(true);
            $table->integer('viewer_count')->default(0);
            $table->integer('total_likes')->default(0);
            $table->decimal('total_sales_amount', 12, 2)->default(0.00);
            $table->unsignedBigInteger('total_gift_coins')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->boolean('is_boosted')->default(false); // discovery boost for new sellers
            $table->integer('failover_count')->default(0); // Hetzner / BunnyCDN failover tracking
            $table->timestamps();
        });

        Schema::create('stream_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained('streams')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('username_display')->nullable();
            $table->text('message');
            $table->boolean('is_bot')->default(false); // automated warm-up bot messages (SRS #11)
            $table->string('message_type')->default('chat'); // chat, gift, bid, system, pinned
            $table->timestamps();
        });

        Schema::create('pk_battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream1_id')->constrained('streams')->cascadeOnDelete();
            $table->foreignId('stream2_id')->nullable()->constrained('streams')->nullOnDelete();
            $table->foreignId('host1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('host2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('host1_score')->default(0);
            $table->unsignedBigInteger('host2_score')->default(0);
            $table->integer('duration_seconds')->default(300); // 5 min countdown
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pk_battles');
        Schema::dropIfExists('stream_messages');
        Schema::dropIfExists('streams');
    }
};
