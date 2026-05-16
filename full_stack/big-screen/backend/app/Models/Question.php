<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Question model for survey questions.
 *
 * @property int $id
 * @property int $question_number
 * @property string $title
 * @property string $content
 * @property string $type
 * @property array|null $options
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_number',
        'title',
        'content',
        'type',
        'options'
    ];

    protected $casts = [
        'options' => 'array',
    ];
}