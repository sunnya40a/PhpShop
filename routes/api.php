<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\CategoryController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/login', [AuthController::class, 'Login']);
Route::post('/register', [AuthController::class, 'Register']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    ///////////////// [     For categories       ]//////////////////////
    // Route::get('/categories/list', [CategoryController::class, 'index']);
    // Route::get('/categories/list/{code}', [CategoryController::class, 'show']);
    Route::get('/categories/list', [CategoryController::class, 'handlelist']);
    Route::post('/categories/save', [CategoryController::class, 'store']);
    Route::put('/categories/update', [CategoryController::class, 'update']);
    Route::delete('/categories/delete', [CategoryController::class, 'destroy']);

    ///////////////// [     For purchases       ]//////////////////////
    Route::get('/purchase/list', [PurchaseHistoryController::class, 'handlePurchase']);
    // Route::get('/purchase/list', [PurchaseHistoryController::class, 'ListPurchase']);
    // Route::get('/purchase/list/{PO}', [PurchaseHistoryController::class, 'ShowPurchase']);
    Route::post('/purchase/save', [PurchaseHistoryController::class, 'SavePurchase']);
    Route::delete('/purchase/delete', [PurchaseHistoryController::class, 'DelPurchase']);
    Route::put('/purchase/update', [PurchaseHistoryController::class, 'UpdatePurchase']);
    Route::get('/purchase/newpo', [PurchaseHistoryController::class, 'LastPO']);
});
