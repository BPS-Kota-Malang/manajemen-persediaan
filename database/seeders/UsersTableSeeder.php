<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            ['id' => 2,
            'name' => 'admin', 
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
        ],
        ]);
    }
}
