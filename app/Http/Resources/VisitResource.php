<?php

namespace App\Http\Resources;

use App\Visit;
use Illuminate\Http\Resources\Json\Resource;

class VisitResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => Visit::$STATUS[$this->status],
            'status_key' => $this->status,
            'date' => $this->start->format('d M Y'),
            'time' => $this->start->format('h:i a'),
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => isset($this->user) ? UserResource::make($this->user): null,
            'partner' => isset($this->partner) ? UserResource::make($this->partner): null,
            'nook' => isset($this->nook) ? NooksResource::make($this->nook): null,
        ];
    }
}
