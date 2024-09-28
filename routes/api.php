<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\SalesHistoryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\UnitlistController;
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
    ///////////////// [     For Unit Lists       ]//////////////////////
    Route::get('/unitlist/list', [UnitlistController::class, 'dropdownlist']);
    ///////////////// [     For categories       ]//////////////////////
    // Route::get('/categories/list', [CategoryController::class, 'index']);
    // Route::get('/categories/list/{code}', [CategoryController::class, 'show']);
    Route::get('/categories/list', [CategoryController::class, 'handlelist']);
    Route::post('/categories/save', [CategoryController::class, 'store']);
    Route::put('/categories/update', [CategoryController::class, 'update']);
    Route::delete('/categories/delete', [CategoryController::class, 'destroy']);

    ///////////////// [     For purchases       ]//////////////////////
    Route::get('/purchase/list', [PurchaseHistoryController::class, 'handlePurchase']);
    Route::get('/purchase/detaillist', [PurchaseHistoryController::class, 'detailelist']);
    // Route::get('/purchase/list', [PurchaseHistoryController::class, 'ListPurchase']);
    // Route::get('/purchase/list/{PO}', [PurchaseHistoryController::class, 'ShowPurchase']);
    Route::post('/purchase/save', [PurchaseHistoryController::class, 'SavePurchase']);
    Route::delete('/purchase/delete', [PurchaseHistoryController::class, 'DelPurchase']);
    Route::put('/purchase/update', [PurchaseHistoryController::class, 'UpdatePurchase']);
    Route::get('/purchase/newpo', [PurchaseHistoryController::class, 'LastPO']);

    ///////////////// [     For Sales       ]//////////////////////
    Route::get('/sales/list', [SalesHistoryController::class, 'handleSales']);
    //// Route::get('/sales/list', [SalesHistoryController::class, 'index']);
    //// Route::get('/sales/list/{invoice}', [SalesHistoryController::class, 'show']);
    //Route::post('/sales/save', [SalesHistoryController::class, 'SavePurchase']);
    //Route::delete('/sales/delete', [SalesHistoryController::class, 'DelPurchase']);
    //Route::put('/sales/update', [SalesHistoryController::class, 'UpdatePurchase']);
    //Route::get('/sales/newinvoice', [SalesHistoryController::class, 'LastPO']);


    ///////////////// [     For Inventory       ]//////////////////////
    Route::get('/inventory/minilist', [InventoryController::class, 'dropdownlist']);
    Route::get('/inventory/list', [InventoryController::class, 'handleInventory']);
    Route::post('/inventory/save', [InventoryController::class, 'store']);
    Route::put('/inventory/update', [InventoryController::class, 'update']);
    Route::delete('/inventory/delete', [InventoryController::class, 'destroy']);
    ///////////////// [     For  Suppler       ]//////////////////////
    Route::get('/suppliers/minilist', [SuppliersController::class, 'dropdownlist']);
    Route::get('/suppliers/list', [SuppliersController::class, 'handlesupplier']);
    Route::post('/suppliers/save', [SuppliersController::class, 'store']);
    Route::put('/suppliers/update', [SuppliersController::class, 'update']);
    Route::delete('/suppliers/delete', [SuppliersController::class, 'destroy']);
});
