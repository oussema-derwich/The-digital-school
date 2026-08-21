<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cryptocurrency;

$user = User::where('email', 'admin@bitchest.com')->first();
$token = $user->createToken('test_token')->plainTextToken;

echo "Token for admin@bitchest.com:\n";
echo $token . "\n";
