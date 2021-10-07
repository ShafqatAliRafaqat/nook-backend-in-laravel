<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $settings = [
            ['key' => 'service_fee_percent','value' => 20],
            ['key' => 'user_can_give_notice','value' => 7],
            ['key' => 'user_can_cancel_notice','value' => 4],
            ['key' => 'password_reset_code_expiry','value' => 10]
        ];

        foreach ($settings as $setting){
            Setting::create($setting);
        }

    }
}
