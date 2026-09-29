<?php

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/inventory', function () {
    return response()->json(
        InventoryItem::latest()->get()
    );
});

//test docker cache with some comment