<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$cats = App\Models\Category::all();
echo "Total categories in DB: " . $cats->count() . "\n";
foreach ($cats as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Slug: {$c->slug} | Icon: {$c->icon} | Active: {$c->is_active}\n";
}
