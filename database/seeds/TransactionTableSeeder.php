<?php

use App\Transaction;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        $statuses = [
            'pending',
            'in_progress',
            'approved',
            'rejected',
        ];

        for ($i = 1; $i <= 2000; $i++) {
            Transaction::create([
                'amount' => rand(1000,5000),
                'details' => substr($faker->text, 0, 250),
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'receipt_id' => rand(0,9),
                'user_id' => rand(0,99),
                'nook_id' => rand(0,9),
            ]);
        }
    }
}
