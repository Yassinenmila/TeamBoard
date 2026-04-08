<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReunionController;
use App\Http\Controllers\Api\DemandeController;


Route::get('/test', function () {
    return response()->json([
        'message' => 'API fonctionne 🚀'
    ]);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'me']);
    Route::apiResource('taches', \App\Http\Controllers\Api\TacheController::class);
    Route::post('/taches/{tache}/assigner', [TacheController::class, 'assigner']);

});

