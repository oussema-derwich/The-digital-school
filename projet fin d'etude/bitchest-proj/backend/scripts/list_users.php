<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$users = \App\Models\User::all();
foreach ($users as $u) {
    echo $u->id . ' ' . $u->email . PHP_EOL;
}
