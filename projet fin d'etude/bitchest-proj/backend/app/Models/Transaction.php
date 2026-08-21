<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_crypto_id',
        'type',
        'quantity',
        'unit_price',
        'total_price',
        'status'
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    /**
     * Table name
     */
    protected $table = 'transactions';

    /**
     * Relation: Transaction appartient à un WalletCrypto
     */
    public function walletCrypto(): BelongsTo
    {
        return $this->belongsTo(WalletCrypto::class, 'wallet_crypto_id');
    }

    /**
     * Relation: Get user through WalletCrypto -> Wallet
     */
    public function user()
    {
        return $this->hasManyThrough(
            User::class,
            Wallet::class,
            'id',           // wallet.id
            'id',           // user.id
            'wallet_crypto_id'  // transactions.wallet_crypto_id -> wallet_cryptos.wallet_id -> wallets.id -> user.id
        );
    }

    /**
     * Relation: Transaction appartient à une Cryptocurrency (via WalletCrypto)
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    /**
     * Check if transaction is a buy
     */
    public function isBuy(): bool
    {
        return $this->type === 'buy';
    }

    /**
     * Check if transaction is a sell
     */
    public function isSell(): bool
    {
        return $this->type === 'sell';
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}

