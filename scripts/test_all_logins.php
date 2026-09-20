<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING WEB LOGIN FOR ALL USER ROLES ===\n\n";

$credentialsToTest = [
    ['login' => 'admin@zaldoris.com', 'password' => 'password', 'expectedRole' => 'admin'],
    ['login' => 'admin', 'password' => 'password', 'expectedRole' => 'admin'],
    ['login' => 'seller@zaldoris.com', 'password' => 'password', 'expectedRole' => 'seller'],
    ['login' => 'supremekicks', 'password' => 'password', 'expectedRole' => 'seller'],
    ['login' => 'creator1@zaldoris.com', 'password' => 'password', 'expectedRole' => 'creator'],
    ['login' => 'elenasparks', 'password' => 'password', 'expectedRole' => 'creator'],
    ['login' => 'buyer@zaldoris.com', 'password' => 'password', 'expectedRole' => 'buyer'],
    ['login' => 'alex_buyer', 'password' => 'password', 'expectedRole' => 'buyer'],
];

foreach ($credentialsToTest as $test) {
    $request = Request::create('/login', 'POST', [
        'email' => $test['login'],
        'password' => $test['password'],
    ]);

    // Add session to request
    $session = app('session')->driver();
    $request->setLaravelSession($session);

    $controller = app(\App\Http\Controllers\Web\AuthController::class);
    $response = $controller->login($request);

    $isLoggedIn = Auth::check();
    $user = Auth::user();

    if ($isLoggedIn && $user && $user->role === $test['expectedRole']) {
        echo " [PASS] Login with '{$test['login']}' -> Authenticated as {$user->name} ({$user->role}) -> Redirect: " . $response->headers->get('Location') . "\n";
    } else {
        echo " [FAIL] Login with '{$test['login']}' -> Failed\n";
    }

    Auth::logout();
}

echo "\n=== TESTING API LOGIN (/api/v1/auth/login) ===\n\n";
$apiController = app(\App\Http\Controllers\Api\V1\AuthController::class);

foreach ($credentialsToTest as $test) {
    $body = json_encode([
        'login' => $test['login'],
        'password' => $test['password'],
    ]);
    $apiRequest = Request::create('/api/v1/auth/login', 'POST', [], [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
        'HTTP_CONTENT_TYPE' => 'application/json'
    ], $body);

    $apiResponse = $apiController->login($apiRequest);
    $data = json_decode($apiResponse->getContent(), true);

    if ($apiResponse->getStatusCode() === 200 && ($data['success'] ?? false)) {
        echo " [PASS] API Login with '{$test['login']}' -> Token generated: " . substr($data['data']['accessToken'] ?? '', 0, 15) . "...\n";
    } else {
        echo " [FAIL] API Login with '{$test['login']}' -> Status: " . $apiResponse->getStatusCode() . " -> " . $apiResponse->getContent() . "\n";
    }
}
