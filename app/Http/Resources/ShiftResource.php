<?php

namespace App\Http\Resources;

use App\Shift;
use Illuminate\Http\Resources\Json\Resource;

class ShiftResource extends Resource {
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'status' => Shift::$STATUS[$this->status],
            'status_key' => $this->status,
            'details' => $this->details,
            'room_type' => $this->room_type,
            'price_per_bed' => $this->price_per_bed,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => UserResource::make($this->user),
            'nook' => NooksResource::make($this->nook),
            'room' => isset($this->room) ? RoomResource::make($this->room): null,
        ];
    }
}
