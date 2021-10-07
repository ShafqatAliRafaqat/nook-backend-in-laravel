<?php

use App\ProductType;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProductTypeTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        $faker = Factory::create();

        for( $i = 1; $i<=10; $i++ ) {

            ProductType::create([
                'name' => $faker->country,
                'order' => ($i-1)
            ]);

        }
    }
}
