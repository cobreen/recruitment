<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        for($i=0; $i < 50;$i++) {
            DB::table('products')->insert([
                'name' => $faker->sentence(3),
                'image' => "https://picsum.photos/seed/" . \rand(1, 1000) . "/200/300",
                'price' => \rand(1, 100),
            ]);
        }
    }
}
