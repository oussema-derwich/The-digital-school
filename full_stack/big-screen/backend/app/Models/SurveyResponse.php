<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SurveyResponse model for storing user survey answers.
 *
 * @property int $id
 * @property string $email
 * @property string $token
 * @property array $answers
 */
class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'token',
        'answers'
    ];

    protected $casts = [
        'answers' => 'array',
    ];
}