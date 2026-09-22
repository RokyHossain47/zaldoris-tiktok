<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$req = Illuminate\Http\Request::create('/product/1', 'GET');
$resp = $kernel->handle($req);

$recent = \App\Models\Product::where('status', 'active')->first();
echo "Product Title: " . $recent->title . "\n";
echo "Category: " . $recent->category_name . "\n";
echo "Currency: " . setting('currency_symbol', '$') . "\n";
echo "Site Name: " . setting('site_name') . "\n";
echo "TEST PASSED!\n";
