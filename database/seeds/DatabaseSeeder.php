<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $this->call(RolesAndPermissionsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(LatLngTableSeeder::class);
        // $this->call(MediaTableSeeder::class);
        // $this->call(NookTableSeeder::class);
        // $this->call(ReviewTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        // $this->call(TransactionTableSeeder::class);
        // $this->call(ComplaintTableSeeder::class);
        // $this->call(BookingTableSeeder::class);
        // $this->call(VisitTableSeeder::class);
        // $this->call(NoticeTableSeeder::class);
        // $this->call(ReceiptTableSeeder::class);
        // $this->call(ShiftTableSeeder::class);
        // $this->call(NotificationSeeder::class);
        $this->call(AreaTableSeeder::class);
        // $this->call(RoomShiftTableSeeder::class);
    }


}
