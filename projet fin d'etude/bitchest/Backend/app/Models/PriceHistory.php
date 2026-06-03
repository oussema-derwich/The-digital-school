<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;
    protected $casts = [
        'date' => 'datetime',
    ];

    protected $fillable = ['cryptocurrency_id', 'value', 'date'];

    public function cryptocurrency()
    {
        return $this->belongsTo(Cryptocurrency::class, 'cryptocurrency_id');
    }
}
