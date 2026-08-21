<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer wallet pour admin
        $admin = User::where('email', 'admin@bitchest.com')->first();
        if ($admin && !$admin->wallet) {
            Wallet::create([
                'user_id' => $admin->id,
                'balance' => 10000,
                'public_address' => 'admin_public_' . $admin->id,
                'private_address' => 'admin_private_' . $admin->id,
            ]);
        }

        // Créer wallet pour user
        $user = User::where('email', 'user@bitchest.com')->first();
        if ($user && !$user->wallet) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 5000,
                'public_address' => 'user_public_' . $user->id,
                'private_address' => 'user_private_' . $user->id,
            ]);
        }
    }
}
