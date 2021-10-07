<?php

use Illuminate\Database\Seeder;
use App\Area;

class AreaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::create([
        	'area' => "DHA",
        	'sub_area'=>'[{
				"name": "Phase 1",
				"locations": [
									{
									"name": "Block A",
									"lat":"31.507179400000002",
									"lng":"74.3278091",
									"radius":"1000"
									},
									{
									"name": "Block B",
									"lat":"31.516179400000002",
									"lng":"74.32380909999999",
									"radius":"1000"
									}
								]
							},
							{
								"name": "Phase 2",
								"locations": [
									{
									"name": "Block C",
									"lat":"31.531179400000003",
									"lng":"74.31580909999998",
									"radius":"1000"
									},
									{
									"name": "Block D",
									"lat":"31.542179400000002",
									"lng":"74.30480909999999",
									"radius":"1000"
									}
								]
							}
						  ]'
        ]);
    }
}
