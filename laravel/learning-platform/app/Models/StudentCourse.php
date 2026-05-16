<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'cours_id',
        'status',
    ];

    // Relation avec le modèle User pour représenter l'étudiant
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation avec le modèle Course pour représenter le cours
    public function course()
    {
        return $this->belongsTo(Cours::class);
    }
}
