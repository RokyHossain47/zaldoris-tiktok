<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reaction;

$reactions = [
    ['name' => 'Fire', 'type' => 'emoji', 'code' => '🔥', 'media_url' => null, 'sort_order' => 1],
    ['name' => 'Heart Eyes', 'type' => 'emoji', 'code' => '😍', 'media_url' => null, 'sort_order' => 2],
    ['name' => 'Red Heart', 'type' => 'emoji', 'code' => '❤️', 'media_url' => null, 'sort_order' => 3],
    ['name' => 'Party Popper', 'type' => 'emoji', 'code' => '🎉', 'media_url' => null, 'sort_order' => 4],
    ['name' => 'Clapping Hands', 'type' => 'emoji', 'code' => '👏', 'media_url' => null, 'sort_order' => 5],
    ['name' => '100 Points', 'type' => 'emoji', 'code' => '💯', 'media_url' => null, 'sort_order' => 6],
    ['name' => 'Rocket', 'type' => 'emoji', 'code' => '🚀', 'media_url' => null, 'sort_order' => 7],
    ['name' => 'Partying Face', 'type' => 'emoji', 'code' => '🥳', 'media_url' => null, 'sort_order' => 8],
    ['name' => 'Money Bag', 'type' => 'emoji', 'code' => '💰', 'media_url' => null, 'sort_order' => 9],
    ['name' => 'Gem Stone', 'type' => 'emoji', 'code' => '💎', 'media_url' => null, 'sort_order' => 10],
    ['name' => 'High Voltage', 'type' => 'emoji', 'code' => '⚡', 'media_url' => null, 'sort_order' => 11],
    ['name' => 'Sparkles', 'type' => 'emoji', 'code' => '✨', 'media_url' => null, 'sort_order' => 12],
    ['name' => 'Thumbs Up', 'type' => 'emoji', 'code' => '👍', 'media_url' => null, 'sort_order' => 13],
    ['name' => 'Star-Struck', 'type' => 'emoji', 'code' => '🤩', 'media_url' => null, 'sort_order' => 14],
    ['name' => 'Shopping Cart', 'type' => 'emoji', 'code' => '🛒', 'media_url' => null, 'sort_order' => 15],
    ['name' => 'Crown', 'type' => 'emoji', 'code' => '👑', 'media_url' => null, 'sort_order' => 16],

    ['name' => 'Dance Celebration', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'sort_order' => 1],
    ['name' => 'Mind Blown', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/26ufdipQqU2lhNA4g/giphy.gif', 'sort_order' => 2],
    ['name' => 'Hype Dance', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/artj92V8o75VPL7AeQ/giphy.gif', 'sort_order' => 3],
    ['name' => 'Popcorn Excited', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/gl0mkIZOW6Nwc/giphy.gif', 'sort_order' => 4],
    ['name' => 'Take My Money', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/sDcfxFDozb3bO/giphy.gif', 'sort_order' => 5],
    ['name' => 'Clapping Wow', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/7rj2ZgttvgomY/giphy.gif', 'sort_order' => 6],
    ['name' => 'Happy Cat', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/JIX9t2j0ZTN9S/giphy.gif', 'sort_order' => 7],
    ['name' => 'Fire Flame Dance', 'type' => 'gif', 'code' => null, 'media_url' => 'https://media.giphy.com/media/yr7n0u3qzO9nG/giphy.gif', 'sort_order' => 8]
];

foreach ($reactions as $r) {
    Reaction::firstOrCreate(['name' => $r['name'], 'type' => $r['type']], $r);
}

echo "Reactions seeded successfully. Total: " . Reaction::count() . "\n";
