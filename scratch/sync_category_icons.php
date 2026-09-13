<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$standardIcons = [
    'fashion-streetwear' => 'bi-universal-access',
    'fashion' => 'bi-universal-access',
    'electronics-gadgets' => 'bi-plug',
    'electronics' => 'bi-plug',
    'beauty-skincare' => 'bi-magic',
    'beauty' => 'bi-magic',
    'home-living' => 'bi-house-door',
    'home' => 'bi-house-door',
    'gaming-esports' => 'bi-controller',
    'gaming' => 'bi-controller',
    'sports-outdoors' => 'bi-dribbble',
    'sports' => 'bi-dribbble',
    'toys-hobbies' => 'bi-box-seam',
    'toys' => 'bi-box-seam',
    'luxury-bags-watches' => 'bi-watch',
    'accessories' => 'bi-watch',
    'sneakers-shoes' => 'bi-fire',
    'collectibles-cards' => 'bi-gem',
];

$categories = [
    ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'bi-universal-access', 'sort_order' => 1],
    ['name' => 'Electronics', 'slug' => 'electronics', 'icon' => 'bi-plug', 'sort_order' => 2],
    ['name' => 'Beauty', 'slug' => 'beauty', 'icon' => 'bi-magic', 'sort_order' => 3],
    ['name' => 'Home', 'slug' => 'home', 'icon' => 'bi-house-door', 'sort_order' => 4],
    ['name' => 'Gaming', 'slug' => 'gaming', 'icon' => 'bi-controller', 'sort_order' => 5],
    ['name' => 'Sports', 'slug' => 'sports', 'icon' => 'bi-dribbble', 'sort_order' => 6],
    ['name' => 'Toys', 'slug' => 'toys', 'icon' => 'bi-box-seam', 'sort_order' => 7],
    ['name' => 'Accessories', 'slug' => 'accessories', 'icon' => 'bi-watch', 'sort_order' => 8],
];

foreach ($categories as $cat) {
    App\Models\Category::updateOrCreate(
        ['slug' => $cat['slug']],
        [
            'name' => $cat['name'],
            'icon' => $cat['icon'],
            'sort_order' => $cat['sort_order'],
            'is_active' => true,
        ]
    );
}

// Clean up any test categories with invalid icon names
foreach (App\Models\Category::all() as $c) {
    if (isset($standardIcons[$c->slug])) {
        $c->icon = $standardIcons[$c->slug];
        $c->save();
    } elseif (!$c->icon || !str_starts_with($c->icon, 'bi')) {
        $c->icon = 'bi-tag-fill';
        $c->save();
    }
}

echo "Categories synced successfully!\n";
foreach (App\Models\Category::where('is_active', true)->orderBy('sort_order', 'asc')->get() as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Icon: {$c->icon}\n";
}
