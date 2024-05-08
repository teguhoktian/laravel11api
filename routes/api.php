<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\BackupController;
use App\Http\Controllers\Settings\GeneralSettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum'])->patch('/user', UserProfileController::class);

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/users', [UserController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Admin'])->get('/users/{user}', [UserController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/users', [UserController::class, 'store']);
Route::middleware(['auth:sanctum', 'role:Admin'])->patch('/users/{user}', [UserController::class, 'update']);
Route::middleware(['auth:sanctum', 'role:Admin'])->delete('/users/{user}', [UserController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/settings', [GeneralSettingController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/settings', [GeneralSettingController::class, 'store']);

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/backup', [BackupController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Admin'])->get('/backup/files', [BackupController::class, 'getFiles']);
Route::middleware(['auth:sanctum', 'role:Admin'])->get('/backup/status', [BackupController::class, 'backupStatuses']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/backup', [BackupController::class, 'createBackup']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/backup/download', [BackupController::class, 'downloadBackup']);
Route::middleware(['auth:sanctum', 'role:Admin'])->delete('/backup/delete', [BackupController::class, 'deleteFile']);

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/roles', [RoleController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Admin'])->get('/roles/{role}', [RoleController::class, 'getRole']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/roles', [RoleController::class, 'store']);
Route::middleware(['auth:sanctum', 'role:Admin'])->patch('/roles/{role}', [RoleController::class, 'update']);
Route::middleware(['auth:sanctum', 'role:Admin'])->delete('/roles/{role}', [RoleController::class, 'destroy']);
