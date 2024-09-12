<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            ['code' => 'P0001', 
            'name' => 'Pulpen 0.5',
            'price' => '5000',
            'stok' => '12',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_id' => 1,
            ],

            ['code' => 'P0002', 
            'name' => 'Kertas HVS',
            'price' => '50000',
            'stok' => '50',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_id' => 1,
            ],

            ['code' => 'P0003', 
            'name' => 'Correction Pen',
            'price' => '3000',
            'stok' => '12',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_id' => 1,
            ],

        ]);
    }
}