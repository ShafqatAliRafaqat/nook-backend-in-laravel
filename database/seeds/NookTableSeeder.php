<?php

use App\LatLng;
use App\Media;
use App\Nook;
use App\Room;
use Faker\Factory;
use Illuminate\Database\Seeder;

class NookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $nookTypes = ['male', 'female', 'both'];

        $spaceTypes = [
            'shared',
            'independent',
        ];

        $types = [
            'house',
            'flat',
            'independentRoom',
            'hostelBuilding',
            'outHouse',
            'other',
        ];

        $status = [
            'pending',
            'inProgress',
            'approved',
            'rejected',
        ];

        for ($i = 1; $i <= 100; $i++) {
            $nook = Nook::create([
                'type' => $types[rand(0, sizeof($types) - 1)],
                'space_type' => $spaceTypes[rand(0, sizeof($spaceTypes) - 1)],
                'gender_type' => $nookTypes[rand(0, sizeof($nookTypes) - 1)],
                'status' => $status[rand(0, sizeof($status) - 1)],
                'nookCode' => 'NK-' . rand(0,100),
                'description' => $faker->text,
                'facilities' => json_encode([
                    'TV', 'AC',
                ]),
                'video_url' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk',
                'number' => $faker->phoneNumber,
                'country' => $faker->country,
                'state' => $faker->city,
                'city' => $faker->city,
                'zipCode' => '54000',
                'address' => $faker->address,
                'area' => rand(1,100),
                'area_unit' => $faker->boolean ? 'marla' : 'Sq feet',
                'inner_details' => '1 Room, 2 Beds',
                'other' => $faker->text,
                'furnished' => $faker->boolean,
                'rent' => rand(10000,50000), 
                'security' => rand(10000,50000),
                'agreementCharges' => rand(500,1000),
                'agreementTenure' => '2 Years',
                'latLng_id' => rand(1,99),
                'partner_id' => rand(10,30)
            ]);

            for ($j = 1; $j <= rand(2,5); $j++) {
                Media::create([
                    'name' => $faker->name,
                    'small' => "/uploads/media/seed/$j.png",
                    'medium' => "/uploads/media/seed/$j.png",
                    'path' => "/uploads/media/seed/$j.png",
                    'nook_id' => $nook->id
                ]);
            }

            for ($j = 1; $j <= rand(10,100); $j++) {
                $capacity = rand(1,6);
                Room::create([
                    'capacity' => $capacity,
                    'noOfBeds' => $capacity,
                    'price_per_bed' => rand(5500,10000),
                    'room_number' => $faker->bothify('Room- ##??'),
                    'nook_id' => $nook->id
                ]);
            }
        }
        for ($i = 101; $i <= 110; $i++) {
            $nook = Nook::create([
                'type' => $types[rand(0, sizeof($types) - 1)],
                'space_type' => $spaceTypes[rand(0, sizeof($spaceTypes) - 1)],
                'gender_type' => $nookTypes[rand(0, sizeof($nookTypes) - 1)],
                'status' => $status[rand(0, sizeof($status) - 1)],
                'nookCode' => 'NK-' . rand(0,100),
                'description' => $faker->text,
                'facilities' => json_encode([
                    'TV', 'AC',
                ]),
                'video_url' => 'https://www.youtube.com/watch?v=W6NZfCO5SIk',
                'number' => $faker->phoneNumber,
                'country' => $faker->country,
                'state' => $faker->city,
                'city' => $faker->city,
                'zipCode' => '54000',
                'address' => $faker->address,
                'area' => rand(1,100),
                'area_unit' => $faker->boolean ? 'marla' : 'Sq feet',
                'inner_details' => '1 Room, 2 Beds',
                'other' => $faker->text,
                'furnished' => $faker->boolean,
                'rent' => rand(10000,50000), 
                'security' => rand(10000,50000),
                'agreementCharges' => rand(500,1000),
                'agreementTenure' => '2 Years',
                'latLng_id' => rand(1,99),
                'partner_id' => 1,
            ]);

            for ($j = 1; $j <= rand(2,5); $j++) {
                Media::create([
                    'name' => $faker->name,
                    'small' => "/uploads/media/seed/$j.png",
                    'medium' => "/uploads/media/seed/$j.png",
                    'path' => "/uploads/media/seed/$j.png",
                    'nook_id' => $nook->id
                ]);
            }

            for ($j = 1; $j <= rand(10,100); $j++) {
                $capacity = rand(1,6);
                Room::create([
                    'capacity' => $capacity,
                    'noOfBeds' => $capacity,
                    'price_per_bed' => rand(5500,10000),
                    'room_number' => $faker->bothify('Room- ##??'),
                    'nook_id' => $nook->id
                ]);
            }
        }

    }
}
