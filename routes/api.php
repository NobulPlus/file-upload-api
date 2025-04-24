<?php

use Illuminate\Support\Facades\Route;

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/upload', [App\Http\Controllers\Api\UploadController::class, 'upload'])->name('api.upload');
Route::get('/download/{token}', [App\Http\Controllers\Api\DownloadController::class, 'download'])->name('api.download');
Route::get('/uploads/stats/{token}', [App\Http\Controllers\Api\UploadController::class, 'stats'])->name('api.stats');