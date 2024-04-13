<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// Route::get('/{any}', function () {
//     return view('welcome');
// })->where('any', '^(?!api|assets|.*\.css|.*\.js).*$'); // Exclude routes that match files in the public directory

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!.*\.css|.*\.js|.*\.jpg|.*\.png|.*\.svg).*$');
