<?php

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test_token')->plainTextToken;
    $this->authHeaders = [
        'Authorization' => "Bearer {$this->token}",
    ];
});

test('unauthenticated request to inventory returns 401', function () {
    $response = $this->getJson('/api/inventory');

    $response->assertStatus(401);
});

test('authenticated user can get inventory items list (200)', function () {
    InventoryItem::create([
        'name' => 'Mouse Gaming',
        'category' => 'Aksesoris',
        'stock' => 10,
        'price' => 300000,
    ]);

    $response = $this->withHeaders($this->authHeaders)
        ->getJson('/api/inventory');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['id', 'name', 'category', 'stock', 'price', 'created_at', 'updated_at'],
            ],
        ]);
});

test('authenticated user can create inventory item (201)', function () {
    $payload = [
        'name' => 'Monitor 4K',
        'category' => 'Elektronik',
        'stock' => 5,
        'price' => 5500000,
    ];

    $response = $this->withHeaders($this->authHeaders)
        ->postJson('/api/inventory', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Monitor 4K')
        ->assertJsonPath('data.stock', 5);

    $this->assertDatabaseHas('inventory_items', [
        'name' => 'Monitor 4K',
        'category' => 'Elektronik',
    ]);
});

test('create inventory item fails with invalid payload returns 422', function () {
    $response = $this->withHeaders($this->authHeaders)
        ->postJson('/api/inventory', [
            'name' => '',
            'stock' => -1,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'category', 'stock', 'price']);
});

test('authenticated user can view single item (200)', function () {
    $item = InventoryItem::create([
        'name' => 'Headset Bluetooth',
        'category' => 'Audio',
        'stock' => 8,
        'price' => 750000,
    ]);

    $response = $this->withHeaders($this->authHeaders)
        ->getJson("/api/inventory/{$item->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Headset Bluetooth');
});

test('view non-existent item returns 404', function () {
    $response = $this->withHeaders($this->authHeaders)
        ->getJson('/api/inventory/9999');

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Item inventaris tidak ditemukan.',
        ]);
});

test('authenticated user can update inventory item (200)', function () {
    $item = InventoryItem::create([
        'name' => 'RAM DDR4 8GB',
        'category' => 'Hardware',
        'stock' => 20,
        'price' => 450000,
    ]);

    $response = $this->withHeaders($this->authHeaders)
        ->putJson("/api/inventory/{$item->id}", [
            'name' => 'RAM DDR4 16GB',
            'category' => 'Hardware',
            'stock' => 15,
            'price' => 850000,
        ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'RAM DDR4 16GB')
        ->assertJsonPath('data.stock', 15);

    $this->assertDatabaseHas('inventory_items', [
        'id' => $item->id,
        'name' => 'RAM DDR4 16GB',
    ]);
});

test('update non-existent item returns 404', function () {
    $response = $this->withHeaders($this->authHeaders)
        ->putJson('/api/inventory/9999', [
            'name' => 'Dummy',
            'category' => 'Dummy',
            'stock' => 1,
            'price' => 1000,
        ]);

    $response->assertStatus(404);
});

test('authenticated user can delete inventory item (200)', function () {
    $item = InventoryItem::create([
        'name' => 'Flashdisk 32GB',
        'category' => 'Storage',
        'stock' => 50,
        'price' => 80000,
    ]);

    $response = $this->withHeaders($this->authHeaders)
        ->deleteJson("/api/inventory/{$item->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Item inventaris berhasil dihapus.',
        ]);

    $this->assertDatabaseMissing('inventory_items', [
        'id' => $item->id,
    ]);
});

test('delete non-existent item returns 404', function () {
    $response = $this->withHeaders($this->authHeaders)
        ->deleteJson('/api/inventory/9999');

    $response->assertStatus(404);
});

