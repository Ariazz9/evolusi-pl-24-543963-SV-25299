<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Laravel Sanctum)
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('user');

    // RESTful API Inventory CRUD
    Route::apiResource('inventory', InventoryItemController::class);
});