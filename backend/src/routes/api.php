<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TacheController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReunionController;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AnnonceController;

Route::get('/test', function () {
    return response()->json(['message' => bcrypt('password')]);
});


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::apiResource('taches',TacheController::class);
    Route::post('/taches/{tache}/assigner', [TacheController::class, 'assigner']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('annonces', AnnonceController::class);
    Route::apiResource('reunions', \App\Http\Controllers\Api\ReunionController::class);
    Route::post('/reunions/{reunion}/inviter', [ReunionController::class, 'inviter']);
    Route::apiResource('demandes', DemandeController::class);
    Route::post('/demandes/{demande}/traiter', [DemandeController::class, 'traiter']);
    Route::apiResource('presences', PresenceController::class);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});



