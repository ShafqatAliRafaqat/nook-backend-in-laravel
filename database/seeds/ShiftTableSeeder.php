<?php

use App\Shift;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ShiftTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $statuses = [
            'pending',
            'in_progress',
            'approved',
            'rejected',
        ];

        for ($i = 1; $i <= 2000; $i++) {
            $capacity = rand(1,6);
            Shift::create([
                'details' => substr($faker->text, 0, 250),
                'room_type' => $capacity,
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'price_per_bed' => rand(5500,10000),
                'user_id' => rand(0,99),
                'nook_id' => rand(0,9),
            ]);
        }
        for ($i = 2001; $i <= 2010; $i++) {
            $capacity = rand(1,6);
            Shift::create([
                'details' => substr($faker->text, 0, 250),
                'room_type' => $capacity,
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'price_per_bed' => rand(5500,10000),
                'user_id' => rand(0,99),
                'nook_id' => rand(101,110),
            ]);
        }
    }
}
