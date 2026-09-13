<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->string('placement')->default('homepage_banner')->after('ad_type'); // homepage_banner, homepage_sidebar, shop_banner, live_stream_banner
            $table->string('subtitle')->nullable()->after('title');
            $table->string('button_text')->nullable()->after('link_url');
        });
    }

    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            $table->dropColumn(['placement', 'subtitle', 'button_text']);
        });
    }
};
