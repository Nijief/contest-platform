<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;

// Публичные маршруты
Route::post('/login', [AuthController::class, 'login']);

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    // Аутентификация
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    // Конкурсы
    Route::apiResource('contests', ContestController::class);
    
    // Подачи работ
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update']);
    Route::post('/submissions/{submission}/submit', [SubmissionController::class, 'submit']);
    Route::post('/submissions/{submission}/change-status', [SubmissionController::class, 'changeStatus']);
    
    // Файлы
    Route::post('/submissions/{submission}/attachments', [AttachmentController::class, 'upload']);
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download']);
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy']);
    
    // Комментарии
    Route::get('/submissions/{submission}/comments', [CommentController::class, 'index']);
    Route::post('/submissions/{submission}/comments', [CommentController::class, 'store']);
});