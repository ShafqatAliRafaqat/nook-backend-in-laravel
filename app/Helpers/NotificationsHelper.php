<?php

namespace App\Helpers;
use App\Notification;

class NotificationsHelper {

    public static function SEND($notification, $user, $app_id){

        try {
        
            $url= "https://expopush.applet.solutions/api/v1/en/user/pushNotifications/send";

            $options = array(
                'http' => array(
                'method'  => 'POST',
                'content' => json_encode([
                    'notification' => [
                        'title' => $notification['title'],
                        'body' => $notification['body'],
                        'sound' => 'default',
                        'timeToLive' => 1200,
                        'payload' => [
                            'type' => 'general'
                        ],
                    ],
                    'users' => [$user],
                    'app_id' => $app_id,
                ]),
                'header'=>  "Content-Type: application/json\r\n" .
                            "Accept: application/json\r\n"
                )
            );
            
            $context  = stream_context_create( $options );
            $result = file_get_contents( $url, false, $context );

            json_decode($result);
          
        } catch (\Throwable $th) {
            //throw $th;
        }

        return  Notification::create([
            'title' => $notification['title'],
            'body' => $notification['body'],
            "user_id" => $user,
        ]); 
    }

}