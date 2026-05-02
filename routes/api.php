<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\SystemSettingController;

Route::get('/music-list', [MusicController::class, 'list']);
Route::post('/convert', [MusicController::class, 'convert']);
Route::post('/convert-file', [MusicController::class, 'uploadVideo']);
Route::post('/convert-source-url', [MusicController::class, 'convertSourceUrl']);
Route::post('/upload-video', [MusicController::class, 'uploadVideo']);
Route::get('/upload-video', function () {
    return response()->json([
        'message' => 'This endpoint accepts POST uploads only. Use the dashboard upload form to send multipart form data.'
    ], 405)->header('Allow', 'POST');
});
Route::match(['get', 'post'], '/video-info', [MusicController::class, 'getInfo']);
Route::get('/convert-progress', [MusicController::class, 'progress']);
Route::delete('/music/{filename}', [MusicController::class, 'delete']);
Route::post('/music/rename', [MusicController::class, 'rename']);
Route::get('/recent-mp3', [MusicController::class, 'recent']);
Route::get('/stats', [MusicController::class, 'stats']);
Route::get('/download/{filename}', [MusicController::class, 'download'])->name('music.download')->where('filename', '.*');

// Album CRUD
Route::get('/albums', [AlbumController::class, 'index']);
Route::post('/albums', [AlbumController::class, 'store']);
Route::get('/albums/{album}', [AlbumController::class, 'show']);
Route::put('/albums/{album}', [AlbumController::class, 'update']);
Route::delete('/albums/{album}', [AlbumController::class, 'destroy']);

// System Settings
Route::get('/settings', [SystemSettingController::class, 'index']);
Route::post('/settings', [SystemSettingController::class, 'update']);
Route::post('/settings/upload', [SystemSettingController::class, 'uploadLogo']);
