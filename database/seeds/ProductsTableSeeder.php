<?php

use App\Category;
use App\Product;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        $categories = Category::all();

        foreach ($categories as $c){

            for( $i = 1; $i<=3; $i++ ) {

                $product = Product::create([
                    'name' => $faker->name,
                    'description' => substr($faker->text,0,150),
                    'price' => rand(50,500),
                    'isVeg' => $faker->boolean,
                    'isDeal' => $faker->boolean,
                    'isFamous' => $faker->boolean,
                    'order' => $i,
                    'discount' =>rand(0,10),
                    'media_id' => rand(1,10),
                    'type_id' => rand(1,10),
                    'rest_id' => $c->rest_id,
                    'prep_time' => rand(10,30)
                ]);

                DB::table("category_product")->insert([
                    'category_id' => $c->id,
                    'product_id' => $product->id
                ]);
            }
        }

    }
}
