<?php

namespace App\Http\Resources;

use App\Bookings;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

class UserBookingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
       
        $receipts = DB::table('receipts')->where('user_id',$this->user_id)->where('nook_id',$this->nook_id)->where('room_id',$this->room_id)->first();
       
        return [
            'id' => $this->id,
            'status' => Bookings::$STATUS[$this->status],
            'rent' => $this->rent,
            'room_id' => $this->room_id,
            'security' => $this->security,
            'installments' => $this->installments,
            'paidSecurity' => round($this->paidSecurity),
            'refunedSecurity' => round($this->refunedSecurity),
            'receipts' =>$receipts,
            'user' => isset($this->user) ? UserResource::make($this->user): null,
            'room' => isset($this->room) ? RoomResource::make($this->room): null,
        ];
    }
}
    