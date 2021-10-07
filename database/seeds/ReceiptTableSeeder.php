<?php

use App\Receipt;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Faker\Factory;


class ReceiptTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        $faker = Factory::create();

        $statuses = [
            'unpaid',
            'in_progress',
            'paid'
        ];

        $extras = [
            'Fund' => 100,
            'Others' => 100,
        ];

        for ($i = 1; $i <= 2000; $i++) {
            Receipt::create([
                'month' => $faker->month,
                'rent' => rand(5000,7500),
                'arrears' => rand(0,500),
                'e_units' => rand(0,500),
                'e_unit_cost' => rand(18,23),
                'fine' => rand(0,500),
                'amount' => 0, // calculate at run time
                'latePaymentCharges' => rand(0,500),
                'extras' => json_encode($extras),
                'total_amount' => 0, // calculate at run time
                'received_amount' => rand(2000,4000),
                'remaining_payable' => 0,// calculate at run time
                'late_day_fine' => rand(50,100),
                'due_date' => Carbon::now()->addDays(rand(1,5)),
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'user_id' => rand(0,99),
                'nook_id' => rand(0,9),
                'room_id' => rand(0,9),
            ]);
        }
        for ($i = 2000; $i <= 2010; $i++) {
            Receipt::create([
                'month' => $faker->month,
                'rent' => rand(5000,7500),
                'arrears' => rand(0,500),
                'e_units' => rand(0,500),
                'e_unit_cost' => rand(18,23),
                'fine' => rand(0,500),
                'amount' => 0, // calculate at run time
                'latePaymentCharges' => rand(0,500),
                'extras' => json_encode($extras),
                'total_amount' => 0, // calculate at run time
                'received_amount' => rand(2000,4000),
                'remaining_payable' => 0,// calculate at run time
                'late_day_fine' => rand(50,100),
                'due_date' => Carbon::now()->addDays(rand(1,5)),
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'user_id' => rand(0,99),
                'nook_id' => rand(101,110),
                'room_id' => rand(0,9),
            ]);
        }
    }
}
