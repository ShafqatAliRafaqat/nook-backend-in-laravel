<?php

namespace App\Http\Resources;

use App\Http\Controllers\Auth\APIAuthController;
use App\UserDetails;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return self::toUser($this);
    }

    public static function toUser($u){

        $details = $u->userDetails;

        $data = [
            'id' => $u->id,
            'name' => $u->name,
            'number' => $u->number,
            'is_active' => $u->is_active,
            'city' => isset($details->city)?$details->city:'',
            'address' => isset($details->address)?$details->address:'',
            'occupation' => isset($details->occupation)?$details->occupation:'',
            'gender' => ($details->gender == '') ? '' : UserDetails::$genders[$details->gender],
            'numberVerified' => (bool)$details->numberVerified,
            'aggreedToTerms' => (bool)$details->aggreedToTerms,
            'room' => isset($details->room) ? $details->room: null,
            "profile" => ($details->profile)?env("APP_URL").'/'.$details->profile->path:env("APP_URL")."/uploads/media/avatar.png",
            'created_at' => $u->created_at->format('g:i A, d M Y'),
            'updated_at' => $u->updated_at->format('g:i A, d M Y'),
        ];

        if(Auth::check() && isset($u->token)){
            $token = APIAuthController::getTokenArray($u->token);
            $data = array_merge($data,$token);
        }

        return $data;
    }
}
