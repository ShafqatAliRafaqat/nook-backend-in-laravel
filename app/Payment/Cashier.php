<?php

/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 8/6/2018
 * Time: 11:08 AM
 */

namespace App\Payment;

use Exception;
use Twocheckout;
use Twocheckout_Charge;

class Cashier {

    public static function pay($paymentInfo){

        Twocheckout::privateKey(env('2CHECKOUT_PRIVATE_KEY'));
        Twocheckout::sellerId(env('2CHECKOUT_SELLER_ID'));

        Twocheckout::sandbox((bool)env('2CHECKOUT_IS_SANDBOX'));

        try {

            $charge = Twocheckout_Charge::auth($paymentInfo, 'array');

            if ($charge['response']['responseCode'] == 'APPROVED') {

                return ['success'=> true,'payload' => $charge];
            }

        } catch (Exception $e) {

            return ['success'=> false,'message'=>$e->getMessage()];
        }

        return ['success'=> false,'message'=>__('messages.something.went.wrong')];
    }
}