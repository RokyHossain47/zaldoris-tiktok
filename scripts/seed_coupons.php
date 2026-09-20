<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Coupon;

$initialCoupons = [
    [
        'code' => 'WELCOME10',
        'name' => 'Welcome New Buyer 10% Discount',
        'description' => '10% off on all products for your first orders.',
        'type' => 'percentage',
        'value' => 10.00,
        'min_order_amount' => 0.00,
        'max_discount_amount' => 50.00,
        'usage_limit' => 1000,
        'per_user_limit' => 2,
        'is_active' => true,
    ],
    [
        'code' => 'SAVE25',
        'name' => '$25 Off Summer Special',
        'description' => '$25 flat discount on orders over $100.',
        'type' => 'fixed',
        'value' => 25.00,
        'min_order_amount' => 100.00,
        'max_discount_amount' => null,
        'usage_limit' => 500,
        'per_user_limit' => 1,
        'is_active' => true,
    ],
    [
        'code' => 'VIP20',
        'name' => 'VIP 20% Storewide Savings',
        'description' => '20% off storewide discount on all orders.',
        'type' => 'percentage',
        'value' => 20.00,
        'min_order_amount' => 50.00,
        'max_discount_amount' => 100.00,
        'usage_limit' => 200,
        'per_user_limit' => 3,
        'is_active' => true,
    ]
];

foreach ($initialCoupons as $data) {
    Coupon::updateOrCreate(['code' => $data['code']], $data);
    echo "Seeded/Updated Coupon: {$data['code']} ({$data['type']} - {$data['value']})\n";
}
