<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserAdminResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        $data = UserResource::toUser($this);

        $details = $this->userDetails;

        return array_merge($data,[
            'referral' => $details->referral,
            'ref_code' =>  $details->ref_code
        ]);
    }
}
