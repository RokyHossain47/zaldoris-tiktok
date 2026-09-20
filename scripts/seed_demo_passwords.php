<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$demoUsers = [
    [
        'name' => 'System Admin',
        'username' => 'admin',
        'email' => 'admin@zaldoris.com',
        'role' => 'admin',
        'phone' => '+14155550100',
    ],
    [
        'name' => 'Supreme Kicks & Fashion',
        'username' => 'supremekicks',
        'email' => 'seller@zaldoris.com',
        'role' => 'seller',
        'phone' => '+14155550101',
    ],
    [
        'name' => 'Elena Sparkles',
        'username' => 'elenasparks',
        'email' => 'creator1@zaldoris.com',
        'role' => 'creator',
        'phone' => '+14155550102',
    ],
    [
        'name' => 'Marcus Vance',
        'username' => 'marcusvance',
        'email' => 'creator2@zaldoris.com',
        'role' => 'creator',
        'phone' => '+14155550103',
    ],
    [
        'name' => 'Alex Rivera',
        'username' => 'alex_buyer',
        'email' => 'buyer@zaldoris.com',
        'role' => 'buyer',
        'phone' => '+14155550104',
    ],
    [
        'name' => 'Jane Doe',
        'username' => 'janedoe',
        'email' => 'janedoe@example.com',
        'role' => 'buyer',
        'phone' => '+14155552671',
    ],
];

foreach ($demoUsers as $data) {
    $u = User::where('email', $data['email'])->orWhere('username', $data['username'])->first();
    if ($u) {
        $u->password = Hash::make('password');
        $u->role = $data['role'];
        $u->name = $data['name'];
        $u->username = $data['username'];
        $u->phone = $data['phone'];
        $u->is_suspended = false;
        $u->save();
        echo "Updated demo user: {$u->email} / 'password'\n";
    } else {
        $u = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make('password'),
            'role' => $data['role'],
            'coin_balance' => 500,
        ]);
        echo "Created demo user: {$u->email} / 'password'\n";
    }
}
