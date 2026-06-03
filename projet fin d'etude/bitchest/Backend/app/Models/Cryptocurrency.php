<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'currentPrice', 'inStock'];
    public function cryptoWallets()
    {
        return $this->hasMany(CryptoWallet::class, 'idCrypto');
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class, 'cryptocurrency_id');
    }
    public function alerts()
    {
        return $this->hasMany(Alert::class, 'crypto_id');
    }
}
