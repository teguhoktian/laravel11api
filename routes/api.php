<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->get('/users', [UserController::class, 'index']);
Route::middleware(['auth:sanctum'])->get('/users/{user}', [UserController::class, 'show']);
Route::middleware(['auth:sanctum'])->post('/users', [UserController::class, 'store']);
Route::middleware(['auth:sanctum'])->patch('/users/{user}', [UserController::class, 'update']);
Route::middleware(['auth:sanctum'])->delete('/users/{user}', [UserController::class, 'destroy']);
