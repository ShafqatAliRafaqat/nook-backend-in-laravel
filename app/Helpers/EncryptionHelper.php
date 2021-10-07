<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 7/27/2018
 * Time: 3:28 PM
 */

namespace App\Helpers;

use RNCryptor\RNCryptor\Decryptor;
use RNCryptor\RNCryptor\Encryptor;

class EncryptionHelper {

    public static function encrypt($key,$data){
        $christopher = new Encryptor();
        return $christopher->encrypt($data, $key);
    }

    public static function decrypt($key,$data){
        $christopher = new Decryptor();
        return $christopher->decrypt($data, $key);
    }

}