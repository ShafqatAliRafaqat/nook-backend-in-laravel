<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Complaint;
use App\Bookings;
use App\Room;

class ComplaintResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request){

        // $booking = Bookings::where('user_id',$this->user_id)->where('nook_id',$this->nook_id)->where('status',Bookings::$APPROVED)->first();
        
        // if($booking){
        //     $room = Room::where('id',$booking->room_id)->first();
        // }
        $complainFrom = isset($this->to_user_id) ?  $this->partner : $this->user ; 
        $complainAgainst = isset($this->to_user_id) ?  $this->user : null ; 

        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => Complaint::$TYPES[$this->type],
            'status' => Complaint::$STATUS[$this->status],
            'status_key' => $this->status,
            'type_key' => $this->type,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => UserResource::make($this->user),
            'complainFrom' => UserResource::make($complainFrom),
            'complainAgainst' => isset($complainAgainst)? UserResource::make($complainAgainst):null,
            'nook' => NooksResource::make($this->nook),
            "media" => ($this->media)?env("APP_URL").'/'.$this->media->path:null,
            'room' => isset($this->room) ? RoomResource::make($this->room): null,
        ];
    }
}
