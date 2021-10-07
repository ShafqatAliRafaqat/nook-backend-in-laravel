<?php

use App\LatLng;
use Faker\Factory;
use Illuminate\Database\Seeder;

class LatLngTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $baseLat = 31.5031794;
        $baseLng = 74.3308091;

        for( $i = 1; $i<=100; $i++ ) {
            LatLng::create([
                'lat' => $baseLat,
                'lng' => $baseLng
            ]);
            $baseLat = $baseLat + (rand(0,5)/1000);
            $baseLng = $baseLng - (rand(0,5)/1000);
        }

    }
}
