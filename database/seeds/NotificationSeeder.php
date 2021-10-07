<?php

use Faker\Factory;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $faker = Factory::create();

        for ($i = 1; $i <= 2000; $i++) {
            \App\Notification::create([
                'title' => $faker->name,
                'body' => $faker->text,
                'user_id' => rand(0,99),
            ]);
        }
    }
}
