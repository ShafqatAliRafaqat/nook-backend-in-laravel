<?php

use App\Order;
use App\Product;
use App\ProductOrder;
use App\Restaurant;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {

        $faker = Factory::create();

        for( $i = 1; $i<=5000; $i++ ) {

            $rest_id = rand(1,200);

            $status = [
                'pending',
                'on_way',
                'delivered'
            ];

            $currentTime = rand(0,120);
            $deliveryTime = Carbon::now()->addMinutes($currentTime);
            $input = [
                'delivery_type' => 'home',
                'status' => $status[rand(0,1)],
                'delivery_fee' => rand(0,100),
                'sub_total'=> rand(450,500),
                'total'=> rand(450,600),
                'points' => rand(0,50),
                'service_fee' => rand(50,100),
                'comment' => $faker->text(100),
                'prep_time'=> rand(10,30),
                'pickup_time' => $deliveryTime->subMinutes(rand(10,20)),
                'delivery_time' => $deliveryTime,
                'delivered_at' => Carbon::now()->addMinutes($currentTime+rand(20,60)),
                'name' => $faker->lastName,
                'number' => $faker->phoneNumber,
                'deliver_address' => $faker->address,
                'address_latLng_id' => rand(1,100),
                'location_latLng_id' => rand(1,100),
                'user_id' => rand(1,100),
                'rest_id' => $rest_id
            ];

            if($faker->boolean){
                unset($input['delivered_at']);
            }

            $order = Order::create($input);

            $pCount = rand(3,5);

            for( $p = 1; $p <= $pCount; $p++ ) {

                ProductOrder::create([
                    'name' => $faker->name,
                    'price' =>  rand(50,500),
                    'quantity' => rand(1,5),
                    'instructions' => $faker->text(30),
                    'order_id' => $order->id,
                    'product_id' => rand(1,3000),
                ]);
            }

        }

        // addons

        for( $i = 1; $i<=5000; $i++ ) {
            DB::table('product_order_addons')->insert([
                'product_order_id'=>rand(1,3000),
                'product_addon_id'=>rand(1,3000),
                'options'=> json_encode([
                    ['name' => $faker->lastName,'price'=> rand(50,150)]
                ]),
            ]);
        }


    }
}
