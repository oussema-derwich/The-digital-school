<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'instructor_id', 'description', 'duration', 'difficulty', 'lesson_count', 'quiz_count', 'price', 'student_limit', 'category_id'];


    // Un cours doit avoir un seul instructeur
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    // Un cours doit être associé qu’a une seule catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Un cours peut avoir plusieurs commentaires
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Un cours peut être associé à plusieurs étudiants
    public function students()
    {
        return $this->hasMany(User::class);
    }
}
