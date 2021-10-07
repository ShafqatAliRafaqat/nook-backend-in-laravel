<?php

use App\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        factory(Restaurant::class,200)->create();

        $restaurants = Restaurant::all();

        foreach ($restaurants as $r){

            $r->tags()->attach(rand(1,20));

            DB::table('user_restaurant')->insert([
                'rest_id' => $r->id,
                'user_id' => rand(1,20)
            ]);

        }

    }
}
