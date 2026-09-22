<?php

use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('inventory page can be displayed', function () {
    $response = $this->get('/inventory');

    $response->assertStatus(200);
});

test('inventory item can be created', function () {
    $response = $this->post('/inventory', [
        'name' => 'Laptop',
        'category' => 'Elektronik',
        'stock' => 10,
        'price' => 15000000,
    ]);

    $response->assertRedirect('/inventory');

    $this->assertDatabaseHas('inventory_items', [
        'name' => 'Laptop',
        'category' => 'Elektronik',
        'stock' => 10,
        'price' => 15000000,
    ]);
});

test('inventory item can be updated', function () {
    $item = InventoryItem::create([
        'name' => 'Laptop',
        'category' => 'Elektronik',
        'stock' => 10,
        'price' => 15000000,
    ]);

    $response = $this->put("/inventory/{$item->id}", [
        'name' => 'Laptop Gaming',
        'category' => 'Elektronik',
        'stock' => 5,
        'price' => 20000000,
    ]);

    $response->assertRedirect('/inventory');

    $this->assertDatabaseHas('inventory_items', [
        'id' => $item->id,
        'name' => 'Laptop Gaming',
        'stock' => 5,
        'price' => 20000000,
    ]);
});

test('inventory item can be deleted', function () {
    $item = InventoryItem::create([
        'name' => 'Laptop',
        'category' => 'Elektronik',
        'stock' => 10,
        'price' => 15000000,
    ]);

    $response = $this->delete("/inventory/{$item->id}");

    $response->assertRedirect('/inventory');

    $this->assertDatabaseMissing('inventory_items', [
        'id' => $item->id,
    ]);
});