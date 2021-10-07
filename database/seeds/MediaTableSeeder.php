<?php

use App\Media;
use Faker\Factory;
use Illuminate\Database\Seeder;

class MediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        for( $i = 1; $i<=10; $i++ ) {
            Media::create([
                'name' => $faker->name,
                'small' => "/uploads/media/seed/$i-small.png",
                'medium' => "/uploads/media/seed/$i-medium.png",
                'path' => "/uploads/media/seed/$i.png",
                'nook_id' => rand(1,5)
            ]);
        }
    }
}
