<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default test/admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Inventaris',
                'password' => Hash::make('password'),
            ]
        );

        // Create sample inventory items
        $items = [
            [
                'name' => 'Laptop Lenovo ThinkPad',
                'category' => 'Elektronik',
                'stock' => 15,
                'price' => 12500000,
            ],
            [
                'name' => 'Mouse Wireless Logitech',
                'category' => 'Aksesoris',
                'stock' => 40,
                'price' => 250000,
            ],
            [
                'name' => 'Mechanical Keyboard Keychron',
                'category' => 'Aksesoris',
                'stock' => 25,
                'price' => 1400000,
            ],
            [
                'name' => 'Monitor Dell 24 Inch IPS',
                'category' => 'Elektronik',
                'stock' => 10,
                'price' => 2800000,
            ],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
