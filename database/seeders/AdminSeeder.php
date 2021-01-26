<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@adminland.net',
            'password' => '$2y$10$5fHg3NMR3bIq5U2J58Hk8O83VdrVXjOLvwXlr.EPJ0.rKk.Kbxx/C',
            'is_admin' => '1',
        ]);
    }
}
