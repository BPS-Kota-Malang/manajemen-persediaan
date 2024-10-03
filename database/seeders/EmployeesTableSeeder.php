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
                'nip' => '123456',
                'name' => 'Dina Ayu',
                'no_handphone' => '0812345',
                'user_id' => 1, // Sesuaikan dengan ID user yang ada
                'jabatan_id' => 1, // Sesuaikan dengan ID jabatan yang ada
                'team_id' => 1, // Sesuaikan dengan ID team yang ada
                // 'created_at' => now(),
                // 'updated_at' => now(),
            ],
            [
                'nip' => '654321',
                'name' => 'Adelia',
                'no_handphone' => '089876',
                'user_id' => 2,
                'jabatan_id' => 2,
                'team_id' => 2,
                // 'created_at' => now(),
                // 'updated_at' => now(),
            ],
        ]);
    }
}
