<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'current_price',
        'image',
        'logo_url',
        'description',
        'in_stock'
    ];

    protected $casts = [
        'current_price' => 'decimal:8',
        'in_stock' => 'boolean'
    ];

    /**
     * Table name
     */
    protected $table = 'cryptocurrencies';

    /**
     * Relation: Cryptocurrency a plusieurs WalletCrypto
     */
    public function walletCryptos(): HasMany
    {
        return $this->hasMany(WalletCrypto::class, 'cryptocurrency_id');
    }

    /**
     * Relation: Cryptocurrency a plusieurs PriceHistory
     */
    public function priceHistories(): HasMany
    {
        return $this->hasMany(PriceHistory::class, 'cryptocurrency_id');
    }

    /**
     * Relation: Cryptocurrency a plusieurs Transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'cryptocurrency_id');
    }

    /**
     * Relation: Cryptocurrency a plusieurs Favorites
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'cryptocurrency_id');
    }

    /**
     * Get the latest price history
     */
    public function getLatestPrice()
    {
        return $this->priceHistories()->latest('created_at')->first();
    }

    /**
     * Get price history for the last N days
     */
    public function getPriceHistory($days = 30)
    {
        return $this->priceHistories()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'asc')
            ->get();
    }
}

