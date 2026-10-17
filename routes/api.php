<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LessonController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/courses',            [CourseController::class, 'index']);
    Route::get('/courses/{course}',   [CourseController::class, 'show']);
    Route::post('/courses',           [CourseController::class, 'store']);
    Route::patch('/courses/{course}', [CourseController::class, 'update']);
    Route::delete('/courses/{course}', [CourseController::class, 'destroy']);

    Route::get('/courses/{course}/lessons',              [LessonController::class, 'index']);
    Route::get('/courses/{course}/lessons/{lesson}',     [LessonController::class, 'show']);
    Route::post('/courses/{course}/lessons',             [LessonController::class, 'store']);
    Route::patch('/courses/{course}/lessons/{lesson}',   [LessonController::class, 'update']);
    Route::delete('/courses/{course}/lessons/{lesson}',  [LessonController::class, 'destroy']);

    Route::get('/courses/{course}/comments',
        [CommentController::class, 'indexForCourse']);
    Route::post('/courses/{course}/comments',
        [CommentController::class, 'storeForCourse']);

    Route::get('/courses/{course}/lessons/{lesson}/comments',
        [CommentController::class, 'indexForLesson']);
    Route::post('/courses/{course}/lessons/{lesson}/comments',
        [CommentController::class, 'storeForLesson']);

    Route::get('/courses/{course}/assignments/{assignment}/comments',
        [CommentController::class, 'indexForAssignment']);
    Route::post('/courses/{course}/assignments/{assignment}/comments',
        [CommentController::class, 'storeForAssignment']);

    Route::get('/categories',            [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    Route::post('/courses/{course}/image',                         [ImageController::class, 'forCourse']);
    Route::post('/courses/{course}/lessons/{lesson}/image',        [ImageController::class, 'forLesson']);
    Route::post('/assignments/{assignment}/image',                  [ImageController::class, 'forAssignment']);
    Route::post('/me/image',                                        [ImageController::class, 'forMe']);
});
