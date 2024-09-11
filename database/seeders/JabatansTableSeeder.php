<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('jabatans')->insert([
            ['code' => 'JB0001', 
            'name' => 'Ketua Tim',],

            ['code' => 'JB0002', 
            'name' => 'Anggota Tim',],

        ]);
    }
}
