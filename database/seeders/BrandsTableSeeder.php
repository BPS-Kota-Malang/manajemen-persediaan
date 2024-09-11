<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('brands')->insert([
            ['code' => 'B0001', 
            'name' => 'Sidu',],

            ['code' => 'B0002', 
            'name' => 'Joyko',],

            ['code' => 'B0003', 
            'name' => 'Stadler',],
        ]);
    }
}
