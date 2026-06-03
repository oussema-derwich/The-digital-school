<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{

    public function run(): void
    {
        // Vérifiez si un administrateur existe déjà
        $adminExists = User::where('email', 'admin@example.com')->exists();

        if (!$adminExists) {
            // Créez un administrateur par défaut
            User::create([
                'name' => 'Admin', // Nom de l'administrateur
                'email' => 'admin@example.com', // Adresse email de l'administrateur
                'password' => Hash::make('password'), // Mot de passe (à modifier)
                'role' => 'admin', // Rôle administrateur
            ]);

            $this->command->info('Admin account created successfully!');
        } else {
            $this->command->warn('Admin account already exists.');
        }
    }
}
