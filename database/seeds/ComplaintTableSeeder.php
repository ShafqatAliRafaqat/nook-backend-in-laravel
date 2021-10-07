<?php

use App\Complaint;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ComplaintTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        $types = [
            'internet',
            'cleaning',
            'entertainment',
            'security',
            'food',
            'maintenance',
            'discipline',
            'staff_related',
            'privacy',
            'other'
        ];

        $statuses = [
            'pending',
            'in_progress',
            'approved',
            'rejected',
        ];

        for ($i = 1; $i <= 2000; $i++) {
            Complaint::create([
                'description' => substr($faker->text, 0, 250),
                'type' => $types[rand(0,sizeof($types) - 1 )],
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'user_id' => rand(0,99),
                'nook_id' => rand(0,9),
                'room_id' => rand(0,9),
                'media_id' => rand(0,9),
            ]);
        }
        for ($i = 2001; $i <= 2010; $i++) {
            Complaint::create([
                'description' => substr($faker->text, 0, 250),
                'type' => $types[rand(0,sizeof($types) - 1 )],
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'user_id' => rand(0,99),
                'nook_id' => rand(101,110),
                'room_id' => rand(101,110),
                'media_id' => rand(0,9)
            ]);
        }
    }
}
