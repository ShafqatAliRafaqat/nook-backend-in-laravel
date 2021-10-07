<?php

namespace App\Helpers;

class SMSSendingHelper {

    private $host;
    private $endPoint;
    private $key;

    public function __construct() {
        $this->host = env('SMS_HOST', null);
        $this->endPoint = env('SMS_END_POINT', null);
        $this->key = env('SMS_KEY', null);
    }

    public function send($number,$message){
        $code = env('SMS_COUNTRY_CODE','+92');
        $number = $code.substr($number,1);
        file_get_contents('https://sendpk.com/api/sms.php?username=923014799372&password=welcomenookhere1234567&sender=88434&mobile='.$number.'&message='.urlencode($message));
        return true;
    }

}