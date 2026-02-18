<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContestController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // Все остальные маршруты требуют аутентификации
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Конкурсы (только админ может создавать/обновлять/удалять)
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