<?php

use App\Review;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ReviewTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        for( $i = 1; $i<=5000; $i++ ) {
            Review::create([
                'ratting' => rand(0,5),
                'comment' => substr($faker->text,0,150),
                'user_id' => rand(1,100),
                'nook_id' => rand(1,200),
            ]);
        }
    }
}
