<?php

namespace App\Http\Resources;

use App\Nook;
use Illuminate\Http\Resources\Json\Resource;
use PhpParser\ErrorHandler\Collecting;

class AreaResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request){
        return self::area($this);
    }


    public static function area($a){

        return [
            'id' => $a->id,
            'area' => $a->area,
            'sub_area' => isset($a->sub_area)? json_decode($a->sub_area):'',
            'created_at' => $a->created_at->format('g:i A, d M Y'),
            'updated_at' => $a->updated_at->format('g:i A, d M Y'),
        ];
    }

}
