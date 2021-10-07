<?php

use App\User;
use App\Media;
use App\UserDetails;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $users = 100;

        $faker = Factory::create();

        for( $j = 1; $j<=$users; $j++ ) {

           $number = "03".$faker->randomNumber(9);

           if($j == 1){
               $number = '03144221255';
           }

           $user = User::create([
                'name' => $faker->name,
                'number' => $number,
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
                'remember_token' => str_random(10),
           ]);

           if($j == 1){
               $user->assignRole(env('SUPER_ADMIN_ROLE_NAME','Admin'));
           }

        }


        $genders = ['male','female'];

        for( $i = 1; $i<=$users; $i++ ) {

            $index = rand(1,6);

            $media = Media::create([
                'name' => $faker->name,
                'small' => "uploads/media/seed/profiles/$index-small.jpg",
                'medium' => "uploads/media/seed/profiles/$index-medium.jpg",
                'path' => "uploads/media/seed/profiles/$index.jpg",
                'nook_id' => 0,
            ]);

            UserDetails::create([
                'user_id' => $i,
                'gender' => $genders[rand(0,1)],
                'profile_id' => $media->id,
                'room_id' => 0,
                'nook_id' => 0,
            ]);
        }

    }
}
