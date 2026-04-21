<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractItemController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ServiceOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/', function () {
    return response()->json(['message' => 'API funcionando!']);
});

Route::prefix('v1')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

Route::apiResource('clients', ClientController::class);
Route::apiResource('contracts', ContractController::class);
Route::apiResource('contract-items', ContractItemController::class)->except(['index', 'show']);
Route::apiResource('billings', BillingController::class)->except(['show', 'delete']);
Route::apiResource('service-orders', ServiceOrderController::class);

