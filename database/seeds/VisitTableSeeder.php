<?php

use App\Visit;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VisitTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $statuses = [
            'pending',
            'in_progress',
            'approved',
            'rejected',
        ];
        $now = Carbon::now();

        for ($i = 1; $i < 100; $i++) {
            $hours = rand(1,5);
            $slot = rand(1,2);
            $startTime = $now->addHours($hours);
            $endTime = $now->addHours($hours + $slot);
            Visit::create([
                'status' => $statuses[rand(0,sizeof($statuses) - 1 )],
                'start' => $startTime,
                'end' => $endTime,
                'user_id' => rand(0,99),
                'partner_id' => rand(0,99),
                'nook_id' => rand(0,9),
            ]);
        }
    }
}
