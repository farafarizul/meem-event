<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerProfileController;
use App\Http\Controllers\Api\GoldSilverPriceController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {
    Route::get('customer/profile', [CustomerProfileController::class, 'show']);
});

Route::get('gold_price', [GoldSilverPriceController::class, 'goldPrice']);
Route::get('silver_price', [GoldSilverPriceController::class, 'silverPrice']);
Route::get('gold_and_silver_price', [GoldSilverPriceController::class, 'goldAndSilverPrice']);
