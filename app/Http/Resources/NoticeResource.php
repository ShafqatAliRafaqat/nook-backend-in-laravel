<?php

namespace App\Http\Resources;

use App\Notice;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class NoticeResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        $diffInDays = $this->checkout->diffInDays(Carbon::now());
        return [
            'id' => $this->id,
            'status' => Notice::$STATUS[$this->status],
            'status_key' => $this->status,
            'details' => $this->details,
            'diffInDays' => $diffInDays > 0 ? $diffInDays : 0,
            'checkout' => $this->checkout->format('d M Y'),
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
            'user' => UserResource::make($this->user),
            'nook' => NooksResource::make($this->nook),
        ];
    }
}
