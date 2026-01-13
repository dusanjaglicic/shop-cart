<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        Product::insert([
            ['name' => 'T-Shirt', 'price' => 19.99, 'stock_quantity' => 10, 'low_stock_threshold' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hoodie', 'price' => 49.99, 'stock_quantity' => 5, 'low_stock_threshold' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cap', 'price' => 14.99, 'stock_quantity' => 2, 'low_stock_threshold' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
