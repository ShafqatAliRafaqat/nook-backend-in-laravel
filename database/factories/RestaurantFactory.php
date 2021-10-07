<?php

use App\Restaurant;
use Faker\Generator as Faker;

$factory->define(Restaurant::class, function (Faker $faker) {

    $statuses = [
        'pending',
        'approved',
    ];

    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'status' => $statuses[rand(0,1)],
        'isVeg' => $faker->boolean,
        'delivery_time' => rand(15,45),
        'min_delivery' => rand(0,500),
        'delivery_fee' => rand(0,200),
        'free_delivery_price' => rand(300,1000),
        'latLng_id' => rand(1,100),
        'media_id' => rand(1,10),
        'about' => $faker->text,
        'address' => $faker->address
    ];
});
