<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Cryptocurrency;
use App\Models\CryptoWallet;
use App\Models\Transaction;
use App\Models\PriceHistory;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create(); // Initialisation de Faker

        // Récupère toutes les cryptos existantes
        $allCryptos = Cryptocurrency::all();

        // Crée 20 utilisateurs
        User::factory(20)->create()->each(function ($user) use ($allCryptos, $faker) {
            // Crée un wallet pour chaque utilisateur
            $wallet = Wallet::factory()->create(['idUser' => $user->id]);

            // Sélectionne un nombre aléatoire de cryptos (1 à 5) pour cet utilisateur
            $userCryptos = $allCryptos->random(rand(1, 5));

            // Pour chaque crypto sélectionnée, simuler plusieurs transactions (achats et ventes)
            $userCryptos->each(function ($crypto) use ($wallet, $faker) {
                // Nombre total d'achats et de ventes aléatoires
                $transactionCount = rand(2, 5);

                for ($i = 0; $i < $transactionCount; $i++) {
                    // Type de transaction (buy ou sell)
                    $transactionType = $faker->randomElement(['buy', 'sell']);

                    // Quantité aléatoire de transaction
                    $quantity = $faker->randomFloat(6, 0.1, 10); // Entre 0.1 et 10 unités

                    // Récupère un prix historique aléatoire
                    $priceHistory = PriceHistory::where('cryptocurrency_id', $crypto->id)
                        ->inRandomOrder()
                        ->first();

                    if (!$priceHistory) {
                        continue; // Passe au prochain achat/vente si aucun prix trouvé
                    }

                    // Vérifie si cette crypto existe déjà dans le wallet
                    $cryptoWallet = CryptoWallet::where('idCrypto', $crypto->id)
                        ->where('idWallet', $wallet->id)
                        ->first();

                    if ($transactionType === 'buy') {
                        // Achat : on ajoute la quantité dans le wallet
                        if ($cryptoWallet) {
                            $cryptoWallet->quantity += $quantity;
                            $cryptoWallet->save();
                        } else {
                            // Crée une nouvelle entrée si l'utilisateur n'a pas encore cette crypto
                            $cryptoWallet = CryptoWallet::create([
                                'idCrypto' => $crypto->id,
                                'idWallet' => $wallet->id,
                                'quantity' => $quantity,
                            ]);
                        }
                    } elseif ($transactionType === 'sell') {
                        // Vente : on soustrait la quantité si possible
                        if ($cryptoWallet && $cryptoWallet->quantity >= $quantity) {
                            $cryptoWallet->quantity -= $quantity;
                            $cryptoWallet->save();
                        } else {
                            continue; // Annule la transaction si pas assez de quantité
                        }
                    }

                    // Enregistre la transaction
                    Transaction::create([
                        'idCryptoWallet' => $cryptoWallet->id,
                        'quantity' => $quantity,
                        'unitPrice' => $priceHistory->value,
                        'totalPrice' => $quantity * $priceHistory->value,
                        'date' => $priceHistory->date,
                        'type' => $transactionType, // Indique s'il s'agit d'un achat ou d'une vente
                    ]);
                }
            });
        });
    }
}