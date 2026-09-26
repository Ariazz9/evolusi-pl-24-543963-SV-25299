<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryItemController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('inventory', InventoryItemController::class)
    ->except(['show'])
    ->parameters([
        'inventory' => 'inventoryItem',
    ]);