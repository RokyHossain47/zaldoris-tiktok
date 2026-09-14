<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$console = $app->make(Illuminate\Contracts\Console\Kernel::class);
$console->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Get Admin
$admin = User::where('role', 'admin')->first();
Auth::login($admin);
echo "Logged in as Admin ID: " . $admin->id . " (" . $admin->email . ")\n";

// Test 1: Admin Auctions Index
$req1 = Request::create('/admin/auctions', 'GET');
$res1 = $kernel->handle($req1);
echo "GET /admin/auctions => Status: " . $res1->getStatusCode() . "\n";

// Test 2: Admin Create Auction Page
$req2 = Request::create('/admin/auctions/create', 'GET');
$res2 = $kernel->handle($req2);
echo "GET /admin/auctions/create => Status: " . $res2->getStatusCode() . "\n";

// Test 3: Admin Store Auction
$adminController = new App\Http\Controllers\Web\AdminController();
$storeRequest = Request::create('/admin/auctions', 'POST', [
    'title' => 'Automated Test Auction Live Diamond Ring',
    'description' => '18K White Gold Solitaire Diamond Ring with GIA certification.',
    'starting_bid' => '350.00',
    'reserve_price' => '600.00',
    'min_bid_step' => '15.00',
    'status' => 'active',
    'seller_id' => $admin->id,
    'starts_at' => now()->format('Y-m-d H:i:s'),
    'ends_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
    'image_url' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=800'
]);
$storeResponse = $adminController->storeAuction($storeRequest);
echo "Admin Store Auction => Response redirect to: " . $storeResponse->getTargetUrl() . "\n";

$createdAuction = Auction::where('title', 'Automated Test Auction Live Diamond Ring')->first();
if ($createdAuction) {
    echo "✓ New auction created successfully with ID: " . $createdAuction->id . ", Current Bid: $" . $createdAuction->current_bid . "\n";
} else {
    echo "✗ Failed to create auction.\n";
}

// Test 4: Placing a bid as buyer
$buyer = User::where('role', 'buyer')->first() ?? User::where('id', '!=', $admin->id)->first();
Auth::login($buyer);
echo "Logged in as Buyer ID: " . $buyer->id . "\n";

$auctionController = new App\Http\Controllers\Web\AuctionController();
$bidRequest = Request::create('/auction/' . $createdAuction->id . '/bid', 'POST', [
    'amount' => 370.00,
]);
$bidResponse = $auctionController->placeBid($bidRequest, $createdAuction->id);
echo "Place Bid Response: " . json_encode($bidResponse->getData()) . "\n";

$createdAuction->refresh();
echo "✓ Auction Current Bid after bid: $" . $createdAuction->current_bid . ", Total bids: " . $createdAuction->bids()->count() . "\n";
