<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'idUser',
        'balance',
        'publicAdress',
        'privateAdress',
        'balance'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function cryptoWallets()
    {
        return $this->hasMany(CryptoWallet::class, 'idWallet');
    }
}