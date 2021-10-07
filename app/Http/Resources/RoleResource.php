<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
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
            'created_at' => isset($this->created_at)? $this->created_at->format('g:i A, d M Y') : "",
            'updated_at' => isset($this->updated_at)? $this->updated_at->format('g:i A, d M Y') : "",
        ];
    }
}
