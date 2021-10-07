<?php

use App\Promo;
use App\Restaurant;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class PromoTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        $faker = Factory::create();

        for( $i = 1; $i<=200; $i++ ) {
            Promo::create($this->getArray($faker));
        }

        for( $i = 1; $i<=3; $i++ ) {

            $data = $this->getArray($faker);

            Promo::create(array_merge($data,[
                'type' => (rand(0,1)) ? Promo::$SING_UP : Promo::$REFER_FRIEND,
                'rest_id' => 0,
                'points' => rand(20,50)
            ]));

        }

    }

    private function getArray($faker){
        return [
            'title' => $faker->name,
            'details' => $faker->text,
            'type' => Promo::$DISCOUNT,
            'code' => str_random(10),
            'discount' => rand(10,30),
            'maxAmount' => rand(50,100),
            'expiry' => Carbon::now()->addDays(rand(10,20)),
            'rest_id' => rand(1,200)
        ];
    }
}
