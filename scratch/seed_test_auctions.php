<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Auction;
use App\Models\User;
use App\Models\Product;
use App\Models\Bid;

$admin = User::where('role', 'admin')->first() ?? User::first();
$seller = User::where('role', 'seller')->first() ?? $admin;
$buyer = User::where('role', 'buyer')->first() ?? $admin;

echo "Admin ID: " . ($admin ? $admin->id : 'none') . "\n";
echo "Seller ID: " . ($seller ? $seller->id : 'none') . "\n";

// Update existing auction #1 to have future ends_at
$a1 = Auction::find(1);
if ($a1) {
    $a1->ends_at = now()->addHours(18);
    $a1->status = 'active';
    $a1->is_blurred = false;
    $a1->save();
    echo "Updated Auction #1 ends_at to future.\n";
}

// Create sample Auction 2 if not exists
if (Auction::count() < 3) {
    $a2 = Auction::create([
        'seller_id' => $seller->id,
        'title' => 'Vintage Rolex Submariner Ref. 5513 (Circa 1974)',
        'description' => "This exquisite Vintage Rolex Submariner (Ref. 5513) dates back to approximately 1974. It features the highly sought-after 'Pre-Comex' dial with a stunning, even vanilla patina on the tritium plots. The 'Ghost' bezel has faded naturally over decades of use to a beautiful misty grey, making it a unique piece for serious collectors. This timepiece has been professionally serviced by our master watchmakers while preserving all original vintage components.",
        'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000&auto=format&fit=crop&q=80',
        'starting_bid' => 2500.00,
        'reserve_price' => 3000.00,
        'current_bid' => 2850.00,
        'min_bid_step' => 25.00,
        'status' => 'active',
        'starts_at' => now()->subHours(6),
        'ends_at' => now()->addHours(12)->addMinutes(35),
        'is_blurred' => false,
    ]);

    Bid::create([
        'auction_id' => $a2->id,
        'user_id' => $buyer->id,
        'amount' => 2850.00,
        'ip_address' => '127.0.0.1',
    ]);

    $a3 = Auction::create([
        'seller_id' => $seller->id,
        'title' => '1986 Fleer Michael Jordan Rookie Card #57 PSA 9 MINT',
        'description' => "Iconic 1986 Fleer Michael Jordan #57 Rookie Card graded PSA 9 MINT. Centered with vibrant red-white-blue borders and razor sharp corners. Comes encased with tamper-proof security sleeve and full provenance certificate.",
        'image_url' => 'https://images.unsplash.com/photo-1613771404784-3a5686aa2be3?w=1000&auto=format&fit=crop&q=80',
        'starting_bid' => 1200.00,
        'reserve_price' => 2000.00,
        'current_bid' => 1650.00,
        'min_bid_step' => 50.00,
        'status' => 'active',
        'starts_at' => now()->subHours(2),
        'ends_at' => now()->addHours(8)->addMinutes(45),
        'is_blurred' => false,
    ]);

    Bid::create([
        'auction_id' => $a3->id,
        'user_id' => $buyer->id,
        'amount' => 1650.00,
        'ip_address' => '127.0.0.1',
    ]);

    $a4 = Auction::create([
        'seller_id' => $seller->id,
        'title' => 'Hermès Birkin 30 Togo Leather Gold Hardware 2023',
        'description' => "Pristine condition Hermès Birkin 30 in Noir Togo calfskin leather with polished gold-plated hardware. Comes complete with clochette, lock, two keys, rain cover, dust bag, and original boutique box.",
        'image_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=1000&auto=format&fit=crop&q=80',
        'starting_bid' => 5000.00,
        'reserve_price' => 8500.00,
        'current_bid' => 5000.00,
        'min_bid_step' => 100.00,
        'status' => 'upcoming',
        'starts_at' => now()->addDays(1),
        'ends_at' => now()->addDays(3),
        'is_blurred' => false,
    ]);

    echo "Sample auctions created successfully!\n";
}

echo "Total Auctions in Database: " . Auction::count() . "\n";
