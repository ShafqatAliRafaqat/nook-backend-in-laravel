<?php

namespace App\Http\Resources;

use App\RoomShift;
use App\Bookings;
use App\Room;
use Illuminate\Http\Resources\Json\Resource;

class RoomShiftResource extends Resource {
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        $booking = Bookings::where('user_id',$this->user_id)->where('status',Bookings::$APPROVED)->first();
        
        if($booking){
            $room = Room::where('id',$booking->room_id)->first();
        }
        
        return [
            'id' => $this->id,
            'status' => RoomShift::$STATUS[$this->status],
            'status_key' => $this->status,
            'details' => $this->details,
            'room_type' => $this->room_type,
            'price_per_bed' => $this->price_per_bed,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => UserDetailResource::make($this->user->userDetails),
            'nook' => NooksResource::make($this->nook),
            'room' => isset($this->room) ? RoomResource::make($this->room): null,
            'current_room' => isset($room) ? $room: null,
        ];
    }
}
