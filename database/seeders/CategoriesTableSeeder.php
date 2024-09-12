<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('categories')->insert([
            ['code' => 'KG0001', 
            'name' => 'ATK',],

            ['code' => 'KG0002', 
            'name' => 'Dokumen',],

        ]);
    }
}
