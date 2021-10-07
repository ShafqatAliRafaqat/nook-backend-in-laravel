<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RoomResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'capacity' => $this->capacity,
            'noOfBeds' => $this->noOfBeds,
            'price_per_bed' => (int)$this->price_per_bed,
            'room_number' => isset($this->room_number) ? $this->room_number :'',
            'users' => isset($this->users) ? UserResource::collection($this->users): [],
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
        ];
    }
}
