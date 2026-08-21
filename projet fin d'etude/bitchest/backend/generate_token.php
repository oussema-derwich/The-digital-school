#!/usr/bin/env php
<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

$user = User::first();
if (!$user) {
    echo "No user found\n";
    exit(1);
}

$token = $user->createToken('api-token')->plainTextToken;
echo "Token: " . $token . "\n";
echo "User ID: " . $user->id . "\n";
echo "User Email: " . $user->email . "\n";
echo "User Role: " . $user->role . "\n";
