<?php

namespace App\Http\Resources;

use App\Nook;
use Illuminate\Http\Resources\Json\Resource;
use PhpParser\ErrorHandler\Collecting;

class NooksResource extends Resource {

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request){
        return self::toNook($this);
    }


    public static function toNook($nook){

        $placeHolder = env("APP_URL")."/uploads/media/placeHolder.jpg";
        $p = [
            "small" => $placeHolder,
            "medium" => $placeHolder,
            "path" => $placeHolder,
        ];

        $medias = (count($nook->medias) > 0) ? MediaResource::collection($nook->medias): [$p];

        return [
            'id' => $nook->id,
            'type' => isset(Nook::$types[$nook->type])? Nook::$types[$nook->type] : '',
            'space_type' => Nook::$SPACE_TYPES[$nook->space_type],
            'gender_type' => isset($nook->gender_type)?Nook::$nookTypes[$nook->gender_type]:'',
            'nookCode' => $nook->nookCode,
            'status' => isset($nook->status)?Nook::$STATUS[$nook->status]:'',
            'description' => $nook->description,
            'video_url' => $nook->video_url,
            'number' => $nook->number,
            'country' => $nook->country,
            'state' => $nook->state,
            'city' => $nook->city,
            'zipCode' => $nook->zipCode,
            'address' => $nook->address,
            'area_data' =>  $nook->area. ' '. $nook->area_unit,
            'area' => $nook->area,
            'area_unit' =>  $nook->area_unit,
            'inner_details' => $nook->inner_details,
            'other' => $nook->other,
            'furnished' => $nook->furnished,
            'rent' => isset($nook->rent)? $nook->rent : '0',
            'security' => $nook->security,
            'noOfBeds' => $nook->noOfBeds,
            'agreementCharges' => $nook->agreementCharges,
            'agreementTenure' => $nook->agreementTenure,
            'securityPercentage' => $nook->securityPercentage,
            'partner_id' => $nook->partner_id,
            'created_at' => $nook->created_at->format('g:i A, d M Y'),
            'updated_at' => $nook->updated_at->format('g:i A, d M Y'),
            'partner' => $nook->partner ? UserResource::make($nook->partner) : null,
            'facilities' => isset($nook->facilities)? json_decode($nook->facilities):'',
            'booking' => $nook->pivot ? $nook->pivot: [],
            'location' => isset($nook->location)?LatLngResource::make($nook->location):'',
            'medias' => $medias,
            'rooms' => ($nook->rooms) ? RoomResource::collection($nook->rooms): [],
            'bookings' => ($nook->bookings) ? UserBookingResource::collection($nook->bookings): [],
        ];
    }

}
