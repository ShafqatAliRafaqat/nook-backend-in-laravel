<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LatLngResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {

        return [
            'lat' => (float)$this->lat,
            'lng' => (float)$this->lng,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
        ];
    }
}
