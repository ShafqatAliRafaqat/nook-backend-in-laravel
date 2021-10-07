<?php

use App\Tag;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        for( $i = 1; $i<=20; $i++ ) {
            Tag::create([
                'name' => $faker->firstName,
                'order' => ($i-1)
            ]);
        }
    }
}
