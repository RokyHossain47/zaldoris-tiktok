<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SellerProfile;
use App\Models\CreatorProfile;
use App\Models\Stream;
use App\Models\StreamMessage;
use App\Models\PkBattle;
use App\Models\Product;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Gift;
use App\Models\CoinPackage;
use App\Models\Advertisement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Core Users
        $admin = User::create([
            'name' => 'System Admin',
            'username' => 'admin',
            'email' => 'admin@zaldoris.com',
            'phone' => '+14165550100',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'admin',
            'coin_balance' => 10000,
            'is_verified' => true,
        ]);

        $sellerUser = User::create([
            'name' => 'Supreme Kicks & Fashion',
            'username' => 'supremekicks',
            'email' => 'seller@zaldoris.com',
            'phone' => '+14165550101',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'seller',
            'coin_balance' => 5000,
            'earnings_usd' => 14250.00,
            'is_verified' => true,
            'fast_shipper_badge' => true,
        ]);

        SellerProfile::create([
            'user_id' => $sellerUser->id,
            'business_name' => 'Supreme Kicks Canada Ltd.',
            'business_number' => 'BN892341098RC0001',
            'id_proof_url' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=500',
            'sin_last4' => '8492',
            'sin_verified_at' => now(),
            'status' => 'verified',
            'live_selling_active' => true,
            'on_time_dispatch_rate' => 98.50,
            'total_orders' => 145,
            'total_dispatches' => 143,
            'audit_status' => 'normal',
            'packing_video_required' => true,
        ]);

        $creatorUser1 = User::create([
            'name' => 'Elena Sparkles',
            'username' => 'elenasparks',
            'email' => 'creator1@zaldoris.com',
            'phone' => '+14165550102',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'creator',
            'coin_balance' => 8400,
            'earnings_usd' => 3890.50,
            'is_verified' => true,
            'vip_badge_tier' => 'Platinum',
        ]);

        CreatorProfile::create([
            'user_id' => $creatorUser1->id,
            'bio' => 'Live Daily 7PM EST! Sneaker reviews, unboxing grails, and mega PK battles! 💎',
            'follower_count' => 45200,
            'following_count' => 120,
            'likes_count' => 284000,
            'subscription_fee' => 7.99,
            'revenue_share_rate' => 60.00,
        ]);

        $creatorUser2 = User::create([
            'name' => 'Marcus Vance',
            'username' => 'marcusvance',
            'email' => 'creator2@zaldoris.com',
            'phone' => '+14165550103',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'creator',
            'coin_balance' => 6200,
            'earnings_usd' => 2710.00,
            'is_verified' => true,
            'vip_badge_tier' => 'Gold',
        ]);

        CreatorProfile::create([
            'user_id' => $creatorUser2->id,
            'bio' => 'Vintage streetwear & rare Pokemon cards collector. Join my live drops! ⚡',
            'follower_count' => 31800,
            'following_count' => 210,
            'likes_count' => 195000,
            'subscription_fee' => 7.99,
            'revenue_share_rate' => 60.00,
        ]);

        $buyerUser = User::create([
            'name' => 'Alex Rivera',
            'username' => 'alex_buyer',
            'email' => 'buyer@zaldoris.com',
            'phone' => '+14165550104',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'coin_balance' => 2500,
            'is_vip' => true,
            'vip_badge_tier' => 'Silver',
            'is_verified' => true,
        ]);

        // 2. 8 Core SRS Gifts (Page 4)
        $gifts = [
            ['name' => 'Glow Heart', 'slug' => 'glow-heart', 'coin_cost' => 10, 'animation_type' => 'heart', 'tier_ladder' => 'standard', 'icon_url' => 'assets/gifts/heart.svg'],
            ['name' => 'Star Wink', 'slug' => 'star-wink', 'coin_cost' => 50, 'animation_type' => 'sparkle', 'tier_ladder' => 'standard', 'icon_url' => 'assets/gifts/star.svg'],
            ['name' => 'Energy Shot', 'slug' => 'energy-shot', 'coin_cost' => 100, 'animation_type' => 'burst', 'tier_ladder' => 'standard', 'icon_url' => 'assets/gifts/energy.svg'],
            ['name' => 'Sonic Bloom', 'slug' => 'sonic-bloom', 'coin_cost' => 300, 'animation_type' => 'wave', 'tier_ladder' => 'vip', 'icon_url' => 'assets/gifts/bloom.svg'],
            ['name' => 'Golden Wave', 'slug' => 'golden-wave', 'coin_cost' => 500, 'animation_type' => 'wave', 'tier_ladder' => 'vip', 'icon_url' => 'assets/gifts/goldwave.svg'],
            ['name' => 'Royal Throne', 'slug' => 'royal-throne', 'coin_cost' => 1000, 'animation_type' => 'crown', 'tier_ladder' => 'exclusive', 'icon_url' => 'assets/gifts/throne.svg'],
            ['name' => 'Dragon Ascend', 'slug' => 'dragon-ascend', 'coin_cost' => 2500, 'animation_type' => 'dragon', 'tier_ladder' => 'exclusive', 'icon_url' => 'assets/gifts/dragon.svg'],
            ['name' => 'Cosmic Empire', 'slug' => 'cosmic-empire', 'coin_cost' => 5000, 'animation_type' => 'cosmic', 'tier_ladder' => 'exclusive', 'icon_url' => 'assets/gifts/cosmic.svg'],
        ];
        foreach ($gifts as $g) {
            Gift::create($g);
        }

        // 3. Coin Packages (SRS Page 4)
        $coinPacks = [
            ['name' => 'Starter Pack', 'coins' => 70, 'price_usd' => 0.99, 'bonus_coins' => 0, 'badge_tier' => null, 'is_popular' => false],
            ['name' => 'Popular Pack', 'coins' => 350, 'price_usd' => 4.99, 'bonus_coins' => 30, 'badge_tier' => 'Bronze', 'is_popular' => true],
            ['name' => 'Value Tier', 'coins' => 700, 'price_usd' => 9.99, 'bonus_coins' => 100, 'badge_tier' => 'Silver', 'is_popular' => false],
            ['name' => 'Pro Supporter', 'coins' => 1400, 'price_usd' => 19.99, 'bonus_coins' => 300, 'badge_tier' => 'Gold', 'is_popular' => false],
            ['name' => 'High Roller', 'coins' => 3500, 'price_usd' => 49.99, 'bonus_coins' => 1000, 'badge_tier' => 'Platinum', 'is_popular' => false],
        ];
        foreach ($coinPacks as $p) {
            CoinPackage::create($p);
        }

        // 4. Products with SRS mandatory declarations
        $prod1 = Product::create([
            'seller_id' => $sellerUser->id,
            'title' => 'Nike Air Jordan 1 Retro High OG Chicago Lost & Found',
            'description' => 'Original deadstock condition, verified authentic. Mandatory natural lighting inspection confirmed.',
            'price' => 380.00,
            'compare_price' => 450.00,
            'stock' => 15,
            'locked_stock' => 5,
            'images' => ['https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600&auto=format&fit=crop&q=80'],
            'category' => 'shoes',
            'sourcing_country' => 'Canada / USA Authorized Retailer',
            'dimensions' => 'US Men 10.5 / Box 35x25x15 cm',
            'is_natural_lighting_declared' => true,
            'status' => 'active',
        ]);

        $prod2 = Product::create([
            'seller_id' => $sellerUser->id,
            'title' => 'Vintage 1996 1st Edition Shadowless Charizard Holo PSA 8',
            'description' => 'Graded gem, holographic scratch-free surface. Packaged securely with insurance and tamper-proof seal.',
            'price' => 1250.00,
            'compare_price' => 1500.00,
            'stock' => 1,
            'locked_stock' => 1,
            'images' => ['https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=600&auto=format&fit=crop&q=80'],
            'category' => 'collectibles',
            'sourcing_country' => 'Japan Pokemon Center Direct',
            'dimensions' => 'PSA Slab Standard 13.5x8 cm',
            'is_natural_lighting_declared' => true,
            'status' => 'active',
        ]);

        $prod3 = Product::create([
            'seller_id' => $sellerUser->id,
            'title' => 'Supreme Box Logo Hoodie FW23 - Heather Grey M',
            'description' => 'Heavyweight crossgrain fleece, embroidered box logo. Direct receipt verification.',
            'price' => 295.00,
            'compare_price' => 340.00,
            'stock' => 8,
            'locked_stock' => 0,
            'images' => ['https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=600&auto=format&fit=crop&q=80'],
            'category' => 'fashion',
            'sourcing_country' => 'Supreme Canada Online Store',
            'dimensions' => 'Chest: 58cm, Length: 71cm',
            'is_natural_lighting_declared' => true,
            'status' => 'active',
        ]);

        // 5. Live Streams (Standard, Live Shopping, Auction, PK Battle)
        $stream1 = Stream::create([
            'host_id' => $sellerUser->id,
            'title' => '🔥 GRAIL DROP! Rare Sneakers & Streetwear Mystery Boxes!',
            'description' => 'Live unboxing & instant 1-tap checkout. Guaranteed 48h fast dispatch!',
            'category' => 'shoes',
            'stream_type' => 'live_shopping',
            'agora_channel' => 'stream_ch_101',
            'agora_token' => 'demo_token_stream_101',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1552346154-21d32810aba3?w=600',
            'is_live' => true,
            'viewer_count' => 1420,
            'total_likes' => 18900,
            'total_sales_amount' => 4820.00,
            'total_gift_coins' => 12500,
            'started_at' => now()->subMinutes(25),
        ]);

        // Pin product to live shopping stream
        $stream1->products()->attach($prod1->id, [
            'is_pinned' => true,
            'session_stock_cap' => 10,
            'session_units_sold' => 4,
        ]);

        $streamAuction = Stream::create([
            'host_id' => $creatorUser2->id,
            'title' => '⚡ $1 START WHATNOT-STYLE AUCTIONS! Charizard & Rare Cards!',
            'description' => 'Fast 60-second countdowns! Real-time bidding with anti-sniping protection.',
            'category' => 'collectibles',
            'stream_type' => 'live_auction',
            'agora_channel' => 'stream_ch_102',
            'agora_token' => 'demo_token_stream_102',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=600',
            'is_live' => true,
            'viewer_count' => 860,
            'total_likes' => 9400,
            'total_sales_amount' => 1850.00,
            'total_gift_coins' => 6400,
            'started_at' => now()->subMinutes(15),
        ]);

        // 6. Live Auction
        $auction1 = Auction::create([
            'seller_id' => $creatorUser2->id,
            'stream_id' => $streamAuction->id,
            'product_id' => $prod2->id,
            'title' => '1996 Shadowless Charizard Holo PSA 8 #4/102',
            'description' => 'Original vintage print. Current high bid from verified buyer. All sales final.',
            'image_url' => 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=600',
            'starting_bid' => 500.00,
            'reserve_price' => 1000.00,
            'current_bid' => 880.00,
            'highest_bidder_id' => $buyerUser->id,
            'min_bid_step' => 20.00,
            'status' => 'active',
            'starts_at' => now()->subMinutes(10),
            'ends_at' => now()->addMinutes(12),
        ]);

        Bid::create([
            'auction_id' => $auction1->id,
            'user_id' => $buyerUser->id,
            'amount' => 880.00,
            'ip_address' => '127.0.0.1',
            'is_flagged_shill' => false,
        ]);

        // 7. PK Battle Stream (Elena vs Marcus)
        $streamPk1 = Stream::create([
            'host_id' => $creatorUser1->id,
            'title' => '⚔️ EPIC CREATOR PK BATTLE! Elena vs Marcus!',
            'description' => '5-minute live gift battle! Send Dragon Ascend and Sonic Bloom to boost the score bar!',
            'category' => 'pk_battle',
            'stream_type' => 'pk_battle',
            'agora_channel' => 'pk_battle_ch_201',
            'agora_token' => 'demo_token_pk_201',
            'thumbnail_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600',
            'is_live' => true,
            'viewer_count' => 3240,
            'total_likes' => 45000,
            'total_gift_coins' => 38000,
            'started_at' => now()->subMinutes(8),
        ]);

        PkBattle::create([
            'stream1_id' => $streamPk1->id,
            'stream2_id' => $streamAuction->id,
            'host1_id' => $creatorUser1->id,
            'host2_id' => $creatorUser2->id,
            'host1_score' => 24500,
            'host2_score' => 18900,
            'duration_seconds' => 300,
            'starts_at' => now()->subMinutes(2),
            'ends_at' => now()->addMinutes(3),
            'status' => 'active',
        ]);

        // 8. Stream Chat Messages
        StreamMessage::create([
            'stream_id' => $stream1->id,
            'user_id' => $buyerUser->id,
            'username_display' => 'alex_buyer',
            'message' => 'Copped the Jordan 1 Chicago! Such a clean pair 🔥',
            'message_type' => 'chat',
        ]);
        StreamMessage::create([
            'stream_id' => $stream1->id,
            'user_id' => null,
            'username_display' => 'SophiaLive',
            'message' => 'Love that product! Showing great detail ✨',
            'is_bot' => true,
            'message_type' => 'chat',
        ]);

        // 9. Advertisements (SRS Page 5)
        Advertisement::create([
            'title' => 'Zaldoris Exclusive Mega Drop - 50% Off Top Streetwear',
            'ad_type' => 'banner',
            'media_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800',
            'link_url' => '/shop',
            'interval_minutes' => 15,
            'is_active' => true,
        ]);

        Advertisement::create([
            'title' => 'Watch & Win: 50 Bonus Coins on joining new creators!',
            'ad_type' => 'interactive',
            'media_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800',
            'link_url' => '/wallet/coins',
            'interval_minutes' => 15,
            'is_active' => true,
        ]);

        // 10. Sample Order with 13% HST & Escrow
        $order = Order::create([
            'order_number' => 'ORD-2026-98124',
            'buyer_id' => $buyerUser->id,
            'seller_id' => $sellerUser->id,
            'stream_id' => $stream1->id,
            'subtotal' => 380.00,
            'shipping_fee' => 10.00,
            'insurance_fee' => 1.90, // > $50 insurance
            'hst_tax' => 50.95, // 13% HST
            'platform_commission' => 26.60, // 7%
            'total_amount' => 442.85,
            'payment_status' => 'escrow_held', // released upon EasyPost delivery confirmation
            'payment_method' => 'stripe_card',
            'customer_email' => 'buyer@zaldoris.com',
            'customer_name' => 'Alex Rivera',
            'shipping_address' => [
                'street' => '120 Bay Street, Suite 800',
                'city' => 'Toronto',
                'province' => 'ON',
                'postal_code' => 'M5J 2R8',
                'country' => 'Canada',
            ],
            'status' => 'packing',
            'dispatch_deadline' => now()->addHours(42),
            'tracking_number' => 'EP982347102CA',
            'carrier' => 'easypost',
            'requires_signature' => true,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $prod1->id,
            'product_title' => $prod1->title,
            'quantity' => 1,
            'unit_price' => 380.00,
            'total_price' => 380.00,
        ]);

        Notification::create([
            'user_id' => $buyerUser->id,
            'title' => '🎉 Order Confirmed ORD-2026-98124',
            'message' => 'Your order has been sent to Supreme Kicks for packing. Seller will dispatch within 48 hours.',
            'type' => 'order_status',
            'action_url' => '/shop/orders',
        ]);
    }
}
