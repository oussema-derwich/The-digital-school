<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        return response()->json(Question::orderBy('question_number')->get());
    }

    public function show(Question $question)
    {
        return response()->json($question);
    }
}