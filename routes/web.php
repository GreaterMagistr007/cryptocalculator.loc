<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\BitcoinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [BitcoinController::class, 'index']);
Route::get('/1', function(){return view('welcome');});

Route::post('/add-points', [BitcoinController::class, 'addPrices']);
Route::post('/get-data', [BitcoinController::class, 'getData']);
