<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletCrypto extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'cryptocurrency_id',
        'quantity',
        'average_buy_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'average_buy_price' => 'decimal:2'
    ];

    /**
     * Table name
     */
    protected $table = 'wallet_cryptos';

    /**
     * Relation: WalletCrypto appartient à un Wallet
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Relation: WalletCrypto contient une Cryptocurrency
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    /**
     * Relation: WalletCrypto a plusieurs Transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'wallet_crypto_id');
    }

    /**
     * Calcule la valeur actuelle de cette crypto
     */
    public function getCurrentValue(): float
    {
        return (float)$this->quantity * (float)$this->cryptocurrency->current_price;
    }

    /**
     * Calcule les gains/pertes
     */
    public function getProfitLoss(): float
    {
        $invested = (float)$this->quantity * (float)$this->average_buy_price;
        $current = $this->getCurrentValue();
        return $current - $invested;
    }

    /**
     * Calcule le pourcentage de profit/loss
     */
    public function getProfitLossPercentage(): float
    {
        $invested = (float)$this->quantity * (float)$this->average_buy_price;
        if ($invested == 0) return 0;
        return (($this->getProfitLoss() / $invested) * 100);
    }
}
