<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('employees')->insert([
            
            [
                'nip' => '654321231224',
                'name' => 'Adelia',
                'no_handphone' => '089876212571',
                'user_id' => 2,
                'jabatan_id' => 2,
                'team_id' => 2,
                // 'created_at' => now(),
                // 'updated_at' => now(),
            ],
        ]);
    }
}
