<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseHistoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'Login']);
Route::post('/register', [AuthController::class, 'Register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/purchase/list', [PurchaseHistoryController::class, 'ListPurchase']);
    Route::get('/purchase/list/{PO}', [PurchaseHistoryController::class, 'ShowPurchase']);
    Route::post('/purchase/save', [PurchaseHistoryController::class, 'SavePurchase']);
    Route::get('/purchase/del/{PO}', [PurchaseHistoryController::class, 'DelPurchase']);
    Route::post('/purchase/update/{PO}', [PurchaseHistoryController::class, 'UpdatePurchase']);
});
