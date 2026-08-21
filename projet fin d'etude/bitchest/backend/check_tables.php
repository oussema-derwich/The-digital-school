<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DATABASE STRUCTURE CHECK ===\n\n";

if (Schema::hasTable('alerts')) {
    echo "✓ Alerts table exists\n";
    echo "Columns:\n";
    $columns = DB::select("DESCRIBE alerts");
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} else {
    echo "✗ Alerts table does not exist\n";
}

echo "\n";

if (Schema::hasTable('favorites')) {
    echo "✓ Favorites table exists\n";
    echo "Columns:\n";
    $columns = DB::select("DESCRIBE favorites");
    foreach ($columns as $col) {
        echo "  - {$col->Field} ({$col->Type})\n";
    }
} else {
    echo "✗ Favorites table does not exist\n";
}
