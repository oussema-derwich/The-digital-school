<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'idCryptoWallet' => $this->faker->numberBetween(1, 10),
            'quantity' => $this->faker->randomFloat(3, 20),
            'unitPrice' => $this->faker->randomFloat(3, 0.1, 10),
            'totalPrice' => $this->faker->randomFloat(3, 0.1, 10),
            'date' => $this->faker->dateTimeThisYear(), // Date aléatoire dans l'année en cours
        ];
    }
}