<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'last_name'];

    // Un instructeur peut être associé à plusieurs cours
    public function courses()
    {
        return $this->hasMany(Cours::class);
    }
}
