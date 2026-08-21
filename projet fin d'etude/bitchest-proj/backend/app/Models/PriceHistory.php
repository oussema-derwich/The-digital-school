<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cryptocurrency_id',
        'value'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'created_at' => 'datetime'
    ];

    /**
     * Table name
     */
    protected $table = 'price_histories';

    /**
     * Relation: PriceHistory appartient à une Cryptocurrency
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }
}

