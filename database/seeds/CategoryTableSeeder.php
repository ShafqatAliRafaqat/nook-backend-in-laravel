<?php

use App\Category;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $faker = Factory::create();

        $restaurants = \App\Restaurant::all();

        foreach ($restaurants as $r){
            for( $i = 1; $i<=5; $i++ ) {
                Category::create([
                    'name' => $faker->name,
                    'description' => substr($faker->text,0,150),
                    'isDeal' => $faker->boolean,
                    'isDeletable' => $faker->boolean,
                    'order' => ($i-1),
                    'rest_id' => $r->id,
                ]);
            }
        }
    }
}
