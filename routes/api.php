<?php

use App\Http\Controllers\Api\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });


     Route::middleware('role:admin')->group(function () {
        Route::get('/admin-only', function () {
            return response()->json(['message' => 'Admin access granted']);
        });
    });

    Route::middleware('role:seller,admin')->group(function () {
        Route::get('/seller-admin', function () {
            return response()->json(['message' => 'Seller/Admin access granted']);
        });
    });

    Route::middleware('role:customer')->group(function () {
        Route::get('/customer-only', function () {
            return response()->json(['message' => 'Customer access granted']);
        });
    });
});
