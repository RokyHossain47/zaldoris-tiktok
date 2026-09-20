<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== USER LIST IN DATABASE ===\n";
$users = User::all();
foreach ($users as $u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email} | Username: {$u->username} | Role: {$u->role} | Password matches 'password': " . (Hash::check('password', $u->password) ? 'YES' : 'NO') . "\n";
}

// Test Auth::attempt with admin@zaldoris.com
echo "\nTesting Auth::attempt with admin@zaldoris.com / password:\n";
$attempt = Auth::attempt(['email' => 'admin@zaldoris.com', 'password' => 'password']);
echo "Attempt result: " . ($attempt ? 'SUCCESS' : 'FAILED') . "\n";

