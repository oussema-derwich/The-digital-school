<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'public_address',
        'private_address'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Relation: Un portefeuille appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: Un portefeuille contient plusieurs cryptos
     */
    public function walletCryptos(): HasMany
    {
        return $this->hasMany(WalletCrypto::class);
    }

    /**
     * Relation: Un portefeuille a plusieurs transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Récupère toutes les cryptos du portefeuille avec leurs données
     */
    public function getCryptos()
    {
        return $this->walletCryptos()->with('cryptocurrency')->get();
    }

    /**
     * Calcule la valeur totale du portefeuille
     */
    public function getTotalValue(): float
    {
        $cryptoValue = $this->walletCryptos()
            ->with('cryptocurrency')
            ->get()
            ->sum(fn($crypto) => $crypto->quantity * $crypto->cryptocurrency->current_price);

        return (float)$this->balance + $cryptoValue;
    }
}
