<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

function testRoute($url, $kernel) {
    $request = Request::create($url, 'GET');
    $response = $kernel->handle($request);
    echo "GET $url => Status: " . $response->getStatusCode() . "\n";
    return $response->getContent();
}

echo "Testing Web Endpoints:\n";
$homeHtml = testRoute('/', $kernel);
if (strpos($homeHtml, 'Vintage Rolex Submariner') !== false || strpos($homeHtml, 'Trending Auctions') !== false) {
    echo "✓ Home page contains dynamic Trending Auctions.\n";
} else {
    echo "✗ Home page failed to find auction titles.\n";
}

$auctionsListHtml = testRoute('/live-auctions', $kernel);
if (strpos($auctionsListHtml, 'Ending Soon') !== false && strpos($auctionsListHtml, 'Vintage Rolex') !== false) {
    echo "✓ Live Auctions list contains dynamic auctions.\n";
} else {
    echo "✗ Live Auctions list failed.\n";
}

$auctionDetailsHtml = testRoute('/auction/2', $kernel);
if (strpos($auctionDetailsHtml, 'Vintage Rolex Submariner Ref. 5513') !== false && strpos($auctionDetailsHtml, 'C$2,850.00') !== false || strpos($auctionDetailsHtml, '2,850.00') !== false) {
    echo "✓ Auction Details page renders dynamic item details and bids.\n";
} else {
    echo "✗ Auction Details page check failed.\n";
}
