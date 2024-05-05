<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'role:Admin'])->get('/users', [UserController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:Admin'])->get('/users/{user}', [UserController::class, 'show']);
Route::middleware(['auth:sanctum', 'role:Admin'])->post('/users', [UserController::class, 'store']);
Route::middleware(['auth:sanctum', 'role:Admin'])->patch('/users/{user}', [UserController::class, 'update']);
Route::middleware(['auth:sanctum', 'role:Admin'])->delete('/users/{user}', [UserController::class, 'destroy']);
