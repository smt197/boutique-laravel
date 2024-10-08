<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DemandeDetteController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\MongoTestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecupArchiveController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TraitementDetteController;
use App\Http\Controllers\UserController;

Route::get('/v1/dettes/archive', [RecupArchiveController::class, 'getAllArchivedDebts']);


Route::post('/v1/dettes', [DetteController::class, 'store']);
Route::post('/v1/send-sms-to-clients', [SmsController::class, 'sendSms']);

Route::get('v1/env', function () {
    return env('ARCHIVE_TYPE');
});

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/token/refresh', [AuthController::class, 'refreshToken']);
});

Route::middleware(['auth:api', 'check.auth'])->prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/articles', ArticleController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/articles/libelle', [ArticleController::class, 'getByLibelle']);
    Route::post('/articles/stock', [ArticleController::class, 'updateStock']);
    Route::patch('/articles/{id}', [ArticleController::class, 'updateStockById']);


    Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);
    Route::patch('/clients/{id}/add-user', [ClientController::class, 'addUserToClient']);
    Route::post('/clients/telephone/', [ClientController::class, 'showClientByTelephone']);
    Route::post('/clients/{id}/dettes', [ClientController::class, 'listDettesClient']);
    Route::post('/clients/{id}/user', [ClientController::class, 'showClientWithUser']);

    Route::apiResource('/users', UserController::class)->only(['index', 'store', 'show']);

    Route::post('/dettes', [DetteController::class, 'store']);
    Route::get('/dettes', [DetteController::class,'index']);
    Route::get('/dettes/{id}', [DetteController::class,'show']);
    Route::get('/dettes/{id}/articles', [DetteController::class,'getDetteWithArticles']);
    Route::get('/dettes/{id}/paiements', [DetteController::class,'getDetteWithPaiements']);
    Route::post('/dettes/{id}/paiements', [DetteController::class, 'StorePaiement']);


    
    Route::get('/archive/clients/{id}/dettes', [RecupArchiveController::class,'getArchivedDebtsByClient']);
    Route::get('/archive/dettes/{Id}', [RecupArchiveController::class,'getArchivedDebtById']);

    Route::post('/restaure/dette/{debtId}', [RecupArchiveController::class,'restore']);

    Route::get('/notification/client/{id}', [NotificationController::class, 'notifySingleClient']);
    Route::post('/notification/client/all', [NotificationController::class, 'sendNotificationToGroup']);
    Route::post('/notification/client/message', [NotificationController::class, 'notifyClientByMessage']);

    //Pour le client
    Route::get('/notification/unread', [NotificationController::class, 'getUnreadNotifications']);
    Route::get('/notification/read', [NotificationController::class, 'getReadNotifications']);



    Route::post('/demandes', [DemandeDetteController::class, 'store']);
    Route::get('/demandes', [DemandeDetteController::class, 'index']);

    Route::post('/demandes/{id}/relance', [DemandeDetteController::class, 'relance']);


    //pour le boutiquier
    Route::get('/demandes/notifications', [NotificationController::class, 'getBoutiquierNotifications']);
    Route::get('/demandes/all', [NotificationController::class, 'getDemandes']);

    //traitement dette
    Route::get('/demandes/{id}/disponible', [TraitementDetteController::class, 'checkDisponibilite']);
    Route::patch('/demandes/{id}', [TraitementDetteController::class, 'update']);














































    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});














