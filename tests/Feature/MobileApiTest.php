<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Stream;
use App\Models\Product;
use App\Models\Auction;
use App\Models\PkBattle;
use App\Models\Gift;
use App\Models\CoinPackage;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_api_health_check(): void
    {
        $response = $this->getJson('/api/v1/health');
        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_mobile_user_registration_and_login(): void
    {
        $regResponse = $this->postJson('/api/v1/auth/register', [
            'name' => 'Mobile Buyer',
            'email' => 'mobile_test@example.com',
            'password' => 'secret123',
            'role' => 'buyer',
            'accepted_terms' => true,
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonStructure(['success', 'token', 'user']);

        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'login' => 'mobile_test@example.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_live_streams_and_agora_token(): void
    {
        $stream = Stream::first();
        $response = $this->getJson("/api/v1/streams/{$stream->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'stream',
                'agora_app_id',
                'agora_token',
                'uid',
            ]);
    }

    public function test_stream_chat_ai_moderation(): void
    {
        $stream = Stream::first();
        $buyer = User::where('role', 'buyer')->first();

        // Valid message
        $response = $this->actingAs($buyer, 'sanctum')->postJson("/api/v1/streams/{$stream->id}/chat", [
            'message' => 'Loving the live stream!'
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // Prohibited message (moderation violation)
        $badResponse = $this->actingAs($buyer, 'sanctum')->postJson("/api/v1/streams/{$stream->id}/chat", [
            'message' => 'This is a free money scam whatsapp me'
        ]);
        $badResponse->assertStatus(422);
    }

    public function test_auction_bidding(): void
    {
        $auction = Auction::first();
        $buyer = User::where('role', 'buyer')->first();

        $newBidAmount = $auction->current_bid + $auction->min_bid_step + 10;
        $response = $this->actingAs($buyer, 'sanctum')->postJson("/api/v1/auctions/{$auction->id}/bid", [
            'amount' => $newBidAmount
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'current_bid' => $newBidAmount]);
    }

    public function test_cart_calculation_with_13_percent_hst(): void
    {
        $product = Product::first();

        $response = $this->postJson('/api/v1/shop/calculate-cart', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'items',
                'breakdown' => [
                    'subtotal',
                    'shipping_fee',
                    'insurance_fee',
                    'hst_tax',
                    'total_amount',
                    'platform_commission',
                ]
            ]);

        $data = $response->json('breakdown');
        $this->assertEquals(13, $data['hst_rate_percent']);
    }

    public function test_pk_battle_and_live_gifting(): void
    {
        $battle = PkBattle::first();
        $buyer = User::where('role', 'buyer')->first();
        $buyer->coin_balance = 500;
        $buyer->save();

        $gift = Gift::first();

        $response = $this->actingAs($buyer, 'sanctum')->postJson("/api/v1/pk-battles/{$battle->id}/send-gift", [
            'gift_id' => $gift->id,
            'target_host_id' => $battle->host1_id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_seller_packing_video_and_dispatch(): void
    {
        $seller = User::where('role', 'seller')->first();
        $order = \App\Models\Order::first();

        // 1. Upload packing video
        $res1 = $this->actingAs($seller, 'sanctum')->postJson("/api/v1/seller/orders/{$order->id}/packing-video", [
            'packing_video_url' => 'https://storage.zaldoris.com/demo.mp4'
        ]);
        $res1->assertStatus(200);

        // 2. Dispatch order
        $res2 = $this->actingAs($seller, 'sanctum')->postJson("/api/v1/seller/orders/{$order->id}/dispatch");
        $res2->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_ai_voice_search(): void
    {
        $response = $this->postJson('/api/v1/ai/voice-search', [
            'voice_transcript' => 'find me rare nike sneakers'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'voice_analysis' => ['detected_category'],
                'matched_products'
            ]);
    }
}
