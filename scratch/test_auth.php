<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// 1. Check GET /login
$reqLogin = Illuminate\Http\Request::create('/login', 'GET');
$respLogin = $kernel->handle($reqLogin);
echo "GET /login Status: " . $respLogin->getStatusCode() . "\n";

// 2. Check GET /register
$reqRegister = Illuminate\Http\Request::create('/register', 'GET');
$respRegister = $kernel->handle($reqRegister);
echo "GET /register Status: " . $respRegister->getStatusCode() . "\n";

// 3. Test Registration POST
$testEmail = 'testuser_' . time() . '@example.com';
$reqPostRegister = Illuminate\Http\Request::create('/register', 'POST', [
    '_token' => csrf_token(),
    'name' => 'Test Shopper',
    'email' => $testEmail,
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'buyer',
    'agree' => '1',
]);
$respPostRegister = $kernel->handle($reqPostRegister);
echo "POST /register Status (Redirect expected 302): " . $respPostRegister->getStatusCode() . "\n";

// Check if user was created
$createdUser = App\Models\User::where('email', $testEmail)->first();
if ($createdUser) {
    echo "SUCCESS: User '{$createdUser->name}' created with ID {$createdUser->id}, Role: {$createdUser->role}, Username: {$createdUser->username}\n";
} else {
    echo "ERROR: User was not created in DB!\n";
}

// 4. Test Login POST
$reqPostLogin = Illuminate\Http\Request::create('/login', 'POST', [
    '_token' => csrf_token(),
    'email' => $testEmail,
    'password' => 'password123',
]);
$respPostLogin = $kernel->handle($reqPostLogin);
echo "POST /login Status (Redirect expected 302): " . $respPostLogin->getStatusCode() . "\n";

echo "ALL AUTH TESTS COMPLETED SUCCESSFULLY!\n";
