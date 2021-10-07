<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class APIAuthController extends Controller {

    public function logout() {
        auth()->logout();
        return ['message' => __('messages.logout.success.message')];
    }

    public function refresh() {
        $token = auth()->refresh();
        return self::getTokenArray($token);
    }


    public static function getTokenArray($token) {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
        ];
    }

}
