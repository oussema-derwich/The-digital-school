<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::get('/questions', [SurveyController::class, 'getQuestions']);
Route::post('/survey', [SurveyController::class, 'submitSurvey']);
Route::get('/responses/{token}', [SurveyController::class, 'getResponse']);

// Routes protégées (admin)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/admin/questions', [QuestionController::class, 'index']);
    Route::get('/admin/responses', [SurveyController::class, 'getAllResponses']);
    Route::get('/admin/statistics', [SurveyController::class, 'getStatistics']);
});