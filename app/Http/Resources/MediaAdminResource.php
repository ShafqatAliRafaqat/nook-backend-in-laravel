<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MediaAdminResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'caption' => $this->caption,
            'alt' => $this->alt,
            'small' => env("APP_URL").'/'.$this->small,
            'medium' => env("APP_URL").'/'.$this->medium,
            'path' => env("APP_URL").'/'.$this->path,
            'nook_id' => $this->nook_id,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
        ];
    }
}
