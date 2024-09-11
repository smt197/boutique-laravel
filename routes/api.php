<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\MongoTestController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;


use Illuminate\Support\Facades\Mail;
use App\Mail\QRCodeMail;

Route::get('/test-email', function () {
    $qrCodeBase64 = base64_encode('dummy qr code data'); // Exemple de QR code base64
    Mail::to('serignembayet@gmail.com')->send(new QRCodeMail($qrCodeBase64));
    return 'Email sent!';
});

Route::post('/v1/dettes', [DetteController::class, 'store']);
Route::post('/v1/send-sms-to-clients', [SmsController::class, 'sendSms']);

Route::get('/v1/test-mongo', [MongoTestController::class, 'testConnection']);

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
    










































    // Ajoutez votre route protégée ici
    Route::get('/route-protegee', function () {
        return 'Bienvenue sur la route protégée !';
    });
});














