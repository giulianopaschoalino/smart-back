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

Route::middleware(['auth:sanctum', 'ability:Admin'])->group(function () {
    Route::apiResource('user', \App\Http\Controllers\UserController::class);
    Route::apiResource('notification', \App\Http\Controllers\NotificationController::class);
    Route::apiResource('faq', \App\Http\Controllers\FaqController::class);
});

Route::middleware(['auth:sanctum', 'ability:Client'])->group(function () {
    Route::post('pld/overview', [\App\Http\Controllers\PldController::class, 'overviewByRegion']);
    Route::post('pld/list', [\App\Http\Controllers\PldController::class, 'listConsumption']);
    Route::post('pld/daily', [\App\Http\Controllers\PldController::class, 'consumptionByDaily']);
    Route::post('pld/schedule', [\App\Http\Controllers\PldController::class, 'consumptionBySchedule']);

    Route::post('economy', [\App\Http\Controllers\EconomyController::class, 'index']);
    Route::post('economy/grossAnnual', [\App\Http\Controllers\EconomyController::class, 'grossAnnualEconomy']);
    Route::post('economy/grossMonthly', [\App\Http\Controllers\EconomyController::class, 'grossMonthlyEconomy']);
    Route::post('economy/estimates', [\App\Http\Controllers\EconomyController::class, 'captiveMonthlyEconomy']);
    Route::post('economy/MWh', [\App\Http\Controllers\EconomyController::class, 'costMWhEconomy']);

    Route::post('operation', [\App\Http\Controllers\OperationSummaryController::class, 'operationSummary']);
});


