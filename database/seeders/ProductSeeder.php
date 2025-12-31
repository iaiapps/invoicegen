<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create products
        Product::create([
            'user_id' => '2',
            'name' => 'Laptop HP Core i5',
            'description' => 'Laptop HP Core i5, RAM 8GB, SSD 256GB',
            'price' => 5000000,
            'sku' => 'LT-HP-001',
        ]);

        Product::create([
            'user_id' => '2',
            'name' => 'Mouse Wireless Logitech',
            'description' => 'Mouse Wireless Logitech M185',
            'price' => 150000,
            'sku' => 'AC-MS-001',
        ]);

        Product::create([
            'user_id' => '2',
            'name' => 'Keyboard Mechanical',
            'description' => 'Keyboard Mechanical RGB',
            'price' => 850000,
            'sku' => 'AC-KB-001',
        ]);
    }
}
