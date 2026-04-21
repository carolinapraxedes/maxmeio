<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ContractItemController;
use App\Http\Controllers\ServiceOrderController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return response()->json(['message' => 'API funcionando!']);
});

// Autenticação
Route::post('/login', [AuthController::class, 'login']);

// Grupo Protegido e com Rate Limit
Route::middleware(['auth:sanctum', 'throttle:cobrancas_limiter'])->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']); // Adicione o logout aqui
    
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    
    Route::get('/cobrancas', [BillingController::class, 'index']);
    
    
   //Route::post('/clientes/{id}/aplicar-credito', [CreditController::class, 'apply']);

    // --- Outros Recursos (CRUDs) ---
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('contracts', ContractController::class);
    Route::apiResource('contract-items', ContractItemController::class);
    Route::apiResource('service-orders', ServiceOrderController::class);
    

});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});