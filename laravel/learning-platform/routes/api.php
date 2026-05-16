<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Routes for Authentication

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Routes for AdminController

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/courses', [AdminController::class, 'index']);
    Route::post('/courses', [AdminController::class, 'store']);
    Route::get('/courses/{id}', [AdminController::class, 'show']);
    Route::put('/courses/{id}', [AdminController::class, 'update']);
    Route::delete('/courses/{id}', [AdminController::class, 'destroy']);

    Route::get('/categories', [AdminController::class, 'showCategory']);
    Route::post('/categories', [AdminController::class, 'addCategory']);
    Route::put('/categories/{id}', [AdminController::class, 'editCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);

    Route::get('/comments', [AdminController::class, 'showComments']);
    Route::post('/comments', [AdminController::class, 'addComment']);
    Route::put('/comments/{id}', [AdminController::class, 'editComment']);
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);

    Route::get('/instructors', [AdminController::class, 'showInstructors']);
    Route::post('/instructors', [AdminController::class, 'addInstructor']);
    Route::put('/instructors/{id}', [AdminController::class, 'editInstructor']);
    Route::delete('/instructors/{id}', [AdminController::class, 'deleteInstructor']);


    Route::get('/feedbacks', [AdminController::class, 'showFeedbacks']);
    Route::delete('/feedbacks/{id}', [AdminController::class, 'deleteFeedback']);

    Route::get('/users', [AdminController::class, 'showUsers']);

    Route::get('/requests', [AdminController::class, 'listAndFilterStudentCourseRequests']);
    Route::put('/requests/{id}/accept', [AdminController::class, 'acceptCourseRequest']);
    Route::put('/requests/{id}/reject', [AdminController::class, 'rejectCourseRequest']);
});

// Routes for StudentController
Route::prefix('student')->middleware('student')->group(function () {
    Route::get('/courses', [StudentController::class, 'index']);
    Route::post('/comments', [StudentController::class, 'leaveComment']);
    Route::post('/register', [StudentController::class, 'registerForCourse']);
    Route::post('/courses/filter', [StudentController::class, 'filterCourses']);
});

// Routes for Not Connected User
Route::prefix('feedback')->group(function () {
    Route::post('/', [UserController::class, 'create']);
});
