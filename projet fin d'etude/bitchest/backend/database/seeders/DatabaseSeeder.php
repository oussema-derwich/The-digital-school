<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call CryptoSeeder first
        $this->call(CryptoSeeder::class);
        
        // Call PriceHistorySeeder
        $this->call(PriceHistorySeeder::class);
        
        // Call WalletSeeder
        $this->call(WalletSeeder::class);
        
        // Création de compte administrateur
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@bitchest.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        
        // Create wallet for admin
        Wallet::create([
            'user_id' => $admin->id,
            'balance' => 10000,
            'public_address' => 'admin_public_' . $admin->id,
            'private_address' => 'admin_private_' . $admin->id,
        ]);

        // Création de compte utilisateur normal
        $user = User::create([
            'name' => 'User',
            'email' => 'user@bitchest.com',
            'password' => bcrypt('user123'),
            'role' => 'client',
            'is_active' => true,
        ]);
        
        // Create wallet for user
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 500,
            'public_address' => 'user_public_' . $user->id,
            'private_address' => 'user_private_' . $user->id,
        ]);
    }
}
