<?php

use App\Restaurant;
use App\TimeSlot;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TimeSlotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        $restaurants = Restaurant::all();

        foreach ($restaurants as $r){
            for( $i = 0; $i<=6; $i++ ) {
                $opening = rand(6,13);
                TimeSlot::create([
                    'day' => $i,
                    'opening' => $opening ,
                    'closing' => $opening + rand(5,8),
                    'rest_id' => $r->id,
                ]);
            }
        }
    }
}
