<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function (){
    Route::post('login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::post('pld/overview', [\App\Http\Controllers\PldController::class, 'overviewByRegion']);
    Route::post('pld/list', [\App\Http\Controllers\PldController::class, 'listConsumption']);
    Route::post('pld/daily', [\App\Http\Controllers\PldController::class, 'consumptionByDaily']);
    Route::post('pld/schedule', [\App\Http\Controllers\PldController::class, 'consumptionBySchedule']);

    Route::post('economy/gross', [\App\Http\Controllers\EconomyController::class, 'grossEconomy']);
    Route::post('economy/accumulated', [\App\Http\Controllers\EconomyController::class, 'accumulatedEconomy']);
    Route::post('economy/estimates', [\App\Http\Controllers\EconomyController::class, 'costEstimatesEconomy']);
    Route::post('economy/MWh', [\App\Http\Controllers\EconomyController::class, 'costMWhEconomy']);

    Route::post('operation', [\App\Http\Controllers\OperationController::class, 'operationSummary']);

    Route::apiResource('user', \App\Http\Controllers\UserController::class);
    Route::apiResource('notification', \App\Http\Controllers\NotificationController::class);
    Route::apiResource('faq', \App\Http\Controllers\FaqController::class);

});


