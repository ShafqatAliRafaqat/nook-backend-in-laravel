<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MediaResource extends Resource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request) {
        $host = env("APP_URL");
        return [
            'id'    => $this->id,
            "small" => $host .'/'. $this->small,
            "medium"=> $host .'/'. $this->medium,
            "path"  => $host .'/'. $this->path,
            'created_at' => $this->created_at->format('g:i A, d M Y'),
            'updated_at' => $this->updated_at->format('g:i A, d M Y'),
        ];
    }
}
