<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            ['code' => 'U0001', 
            'name' => 'pcs',
        ],

            ['code' => 'U0001', 
            'name' => 'pack',],
        ]);
    }
}
