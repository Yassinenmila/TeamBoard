<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReunionController;
use App\Http\Controllers\Api\DemandeController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'me']);
    Route::apiResource('taches',TacheController::class);
    Route::post('/taches/{tache}/assigner', [TacheController::class, 'assigner']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('reunions', \App\Http\Controllers\Api\ReunionController::class);
    Route::post('/reunions/{reunion}/inviter', [ReunionController::class, 'inviter']);
    Route::apiResource('demandes', DemandeController::class);
});

