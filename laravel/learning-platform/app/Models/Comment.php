<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['content', 'user_id', 'cours_id'];

    // Un commentaire doit appartenir a un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un commentaire doit avoir le cours auquel ce commentaire est associé
    public function course()
    {
        return $this->belongsTo(Cours::class);
    }
}
