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
            // 'price' => '5000',
            // 'stok' => '12',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_1' => 'pack',
            'unit_2' => 'pcs',
            //'unit_id' => 1,
            'conversion_rate' => 12,
            'stok' => '12'
            
            ],

            ['code' => 'P0002', 
            'name' => 'Kertas HVS',
            // 'price' => '50000',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_1' => 'box',
            'unit_2' => 'rim',
            //'unit_id' => 1,
            'conversion_rate' => 500,
            'stok' => '50',
            ],

            ['code' => 'P0003', 
            'name' => 'Correction Pen',
            // 'price' => '3000',
            'stok' => '12',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_1' => 'pack',
            'unit_2' => 'pcs',
            //'unit_id' => 1,
            'conversion_rate' => 12,
            'stok' => '12'
            ],


            ['code' => 'P0004', 
            'name' => 'Penggaris',
            // 'price' => '5000',
            'category_id' => 1,
            'brand_id' => 3,
            'unit_1' => 'pack',
            'unit_2' => 'pcs',
            //'unit_id' => 1,
            'conversion_rate' => 10,
            'stok' => '6',
            ],

            ['code' => 'P0005', 
            'name' => 'Selotip',
            // 'price' => '4000',
            'category_id' => 1,
            'brand_id' => 2,
            'unit_1' => 'pack',
            'unit_2' => 'pcs',
            //'unit_id' => 1,
            'conversion_rate' => 12,
            'stok' => '3',
            ],
        ]);
    }
}