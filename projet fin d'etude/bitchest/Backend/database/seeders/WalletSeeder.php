<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class WalletSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Création d'une instance de Faker

        // Récupère tous les utilisateurs
        User::all()->each(function ($user) use ($faker) {
            Wallet::create([
                'idUser' => $user->id,
                'publicAdress' => '0x' . bin2hex(random_bytes(16)), // Adresse publique aléatoire
                'privateAdress' => '0x' . bin2hex(random_bytes(16)), // Adresse privée aléatoire
            ]);
        });
    }
}