<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@bitchest.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        // Créer le wallet pour l'admin
        Wallet::create([
            'user_id' => $admin->id,
            'balance' => 10000,
            'public_address' => 'admin_public_' . $admin->id,
            'private_address' => 'admin_private_' . $admin->id,
        ]);

        // Créer un utilisateur client
        $user = User::create([
            'name' => 'User Test',
            'email' => 'user@bitchest.com',
            'password' => Hash::make('password123'),
            'role' => 'client',
            'is_active' => true
        ]);

        // Créer le wallet pour l'utilisateur
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 500,
            'public_address' => 'user_public_' . $user->id,
            'private_address' => 'user_private_' . $user->id,
        ]);

        // Créer d'autres utilisateurs de test
        for ($i = 1; $i <= 3; $i++) {
            $testUser = User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password123'),
                'role' => 'client',
                'is_active' => true
            ]);

            // Créer le wallet pour chaque utilisateur test
            Wallet::create([
                'user_id' => $testUser->id,
                'balance' => 500,
                'public_address' => 'user' . $i . '_public_' . $testUser->id,
                'private_address' => 'user' . $i . '_private_' . $testUser->id,
            ]);
        }
    }
}
