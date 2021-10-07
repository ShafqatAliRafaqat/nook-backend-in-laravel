<?php

use App\Product;
use App\ProductAddon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class AddonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        $options = json_encode([
            ['name' => 'Coke','price' => 120],
            ['name' => 'Red Bull','price' => 150],
            ['name' => '7-up','price' => 60],
        ]);

        for( $i = 1; $i<=3000; $i++ ) {

            ProductAddon::create([
                'name' => $faker->name,
                'isMultiSelectable' => 0,
                'options' => $options,
                'product_id' => rand(0,3000)
            ]);

        }

    }
}
