<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image'];

    // Une catégorie peut être associé à plusieurs cours
    public function courses()
    {
        return $this->hasMany(Cours::class);
    }
}
