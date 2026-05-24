<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ReactionController;
use Illuminate\Support\Facades\Route;

Route::get('/languages', [LanguageController::class, 'index']);
Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blogs/{id}', [BlogController::class, 'show']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google/callback', [AuthController::class, 'googleCallback']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::post('/blogs/{id}/comment', [CommentController::class, 'store']);
    Route::post('/blogs/{id}/react', [ReactionController::class, 'toggle']);

    Route::middleware('can:create-blog')->post('/blogs', [BlogController::class, 'store']);
    Route::middleware('can:edit-blog')->put('/blogs/{id}', [BlogController::class, 'update']);
});
