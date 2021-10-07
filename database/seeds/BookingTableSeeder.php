<?php

use App\Bookings;
use Illuminate\Database\Seeder;

class BookingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $statuses = [
            'pending',
            'in_progress',
            'approved',
            'rejected',
            'off-board'
        ];

        for ($i = 1; $i < 100; $i++) {
            Bookings::create([
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'rent' => rand(10000,15000),
                'security' => rand(10000,15000),
                'paidSecurity' => rand(10000,15000),
                'user_id' => rand(0,99),
                'nook_id' => rand(0,9),
            ]);
        }
        for ($i = 101; $i < 110; $i++) {
            Bookings::create([
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'rent' => rand(10000,15000),
                'security' => rand(10000,15000),
                'paidSecurity' => rand(10000,15000),
                'user_id' => rand(2,10),
                'nook_id' => rand(101,110),
            ]);
        }
    }
}
