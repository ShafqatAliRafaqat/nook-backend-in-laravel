<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\NotificationsHelper;

use App\Complaint;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\NooksResource;
use App\LatLng;
use App\Nook;
use App\Bookings;
use App\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Location;

class APINookAdminController extends BaseController {
    protected $permissions = [
        'index'=>'nook-list-all',
        'save'=>'nook-create'
    ];

    public function index(Request $request){
        $input = $request->all();

        $qb = Nook::orderBy('updated_at','DESC')->with('medias');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"description",$qb);
        $qb = QB::whereLike($input,"facilities",$qb);
        $qb = QB::where($input,"type",$qb);
        $qb = QB::where($input,"space_type",$qb);
        $qb = QB::where($input,"gender_type",$qb);
        $qb = QB::where($input,"nookCode",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $nooks = $qb->paginate(20);

        $nooks->appends(Input::except('page'));

        return NooksResource::collection($nooks);
    }
    public function allNooks(Request $request){
        
        $input = $request->all();
        
        $qb = Nook::orderBy('updated_at','DESC')->with('rooms');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"description",$qb);
        $qb = QB::whereLike($input,"facilities",$qb);
        $qb = QB::where($input,"type",$qb);
        $qb = QB::where($input,"space_type",$qb);
        $qb = QB::where($input,"gender_type",$qb);
        $qb = QB::where($input,"nookCode",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $nooks = $qb->get();
        return response()->json(['nooks'=>$nooks]);
    }
    public function add(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'type' => 'required',
            'space_type' => 'required',
            'gender_type' => 'required',
            'status' => 'required',
            'nookCode' => 'required',
            'description' => 'present',
            'facilities' => 'present',
            'video_url' => 'required',
            'number' => 'present',
            'country' => 'present',
            'state' => 'present',
            'city' => 'present',
            'zipCode' => 'present',
            'address' => 'present',
            'securityPercentage' => 'required',
            'area' => 'present',
            'area_unit' => 'present',
            'inner_details' => 'present',
            'other' => 'present',
            'furnished' => 'present',
            'rent' => 'present',
            'security' => 'present',
            'agreementCharges' => 'present',
            'agreementTenure' => 'present',
            'lat' => 'required',
            'lng' => 'required',
            'partner_id' => 'required',
            'rooms' => 'present',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $location = LatLng::create([
            'lat' => $input['lat'],
            'lng' => $input['lng'],
        ]);

        $nook = Nook::create([
            'type' => $input['type'],
            'space_type' => $input['space_type'],
            'gender_type' => $input['gender_type'],
            'status' => $input['status'],
            'nookCode' => $input['nookCode'],
            'description' => $input['description'],
            'facilities' => json_encode($input['facilities']),
            'video_url' => $input['video_url'],
            'number' => $input['number'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'zipCode' => $input['zipCode'],
            'address' => $input['address'],
            'securityPercentage' => $input['securityPercentage'],
            'area' => $input['area'],
            'area_unit' => $input['area_unit'],
            'inner_details' => $input['inner_details'],
            'other' => $input['other'],
            'furnished' => $input['furnished'],
            'rent' => $input['rent'],
            'security' => $input['security'],
            'agreementCharges' => $input['agreementCharges'],
            'agreementTenure' => $input['agreementTenure'],
            'latLng_id' => $location->id,
            'partner_id' => $input['partner_id'],
            'noOfBeds' => isset($input['noOfBeds'])?$input['noOfBeds']:0,
        ]);


        foreach ($input['rooms'] as $room){
            Room::create([
                'capacity' => $room['capacity'],
                'noOfBeds' => $room['noOfBeds'],
                'price_per_bed' => $room['price_per_bed'],
                'room_number' => $room['room_number'],
                'nook_id' => $nook->id
            ]);
        }
        if($nook->space_type =="service" && $nook->status == Nook::$APPROVED){
            $approved_booking = Bookings::where("user_id",$input['partner_id'])->where('status',Bookings::$APPROVED)->update([
                'status' => Bookings::$OFFBOARD
            ]);
            $booking = Bookings::create([
                'status' => Bookings::$APPROVED,
                'rent' => $input['rent'],
                'security' => $input['security'],
                'paidSecurity' => 0,
                'user_id' => $input['partner_id'],
                'nook_id' => $nook->id,
                'room_id' => 0,
            ]);
        }
        return [
            'message' => 'Nook created successfully',
            'nook' => NooksResource::make($nook)
        ];
    }

    public function edit(Request $request, $id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'type' => 'required',
            'space_type' => 'required',
            'gender_type' => 'required',
            'status' => 'required',
            'description' => 'present',
            'facilities' => 'present',
            'video_url' => 'required',
            'number' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zipCode' => 'required',
            'address' => 'required',
            'securityPercentage' => 'required',
            'area' => 'present',
            'area_unit' => 'present',
            'inner_details' => 'present',
            'other' => 'present',
            'furnished' => 'present',
            'rent' => 'present',
            'security' => 'present',
            'agreementCharges' => 'present',
            'agreementTenure' => 'present',
            'lat' => 'required',
            'lng' => 'required',
            'partner_id' => 'required',
            'rooms' => 'present',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $nook = Nook::findOrFail($id);

        $location = LatLng::find($nook->latLng_id);
        if($location){
            $location->update([
                'lat' => $input['lat'],
                'lng' => $input['lng'],
            ]);
        }else{
            $location = LatLng::create([
                'lat' => $input['lat'],
                'lng' => $input['lng'],
            ]);
        }

        if($nook->status !== $input['status']){
            NotificationsHelper::SEND([
                'title' => 'Your Nook Status Updated',
                'body' => 'Your nook status updated to ' . $input['status'],
            ],$nook->partner_id, env("PARTNER_APP_ID"));
        }

        Nook::where('id',$id)->update([
            'type' => $input['type'],
            'space_type' => $input['space_type'],
            'gender_type' => $input['gender_type'],
            'status' => $input['status'],
            'nookCode' => $input['nookCode'],
            'description' => $input['description'],
            'facilities' => json_encode($input['facilities']),
            'video_url' => $input['video_url'],
            'number' => $input['number'],
            'country' => $input['country'],
            'state' => $input['state'],
            'city' => $input['city'],
            'zipCode' => $input['zipCode'],
            'address' => $input['address'],
            'securityPercentage' => $input['securityPercentage'],
            'area' => $input['area'],
            'area_unit' => $input['area_unit'],
            'inner_details' => $input['inner_details'],
            'other' => $input['other'],
            'furnished' => $input['furnished'],
            'rent' => $input['rent'],
            'security' => $input['security'],
            'agreementCharges' => $input['agreementCharges'],
            'agreementTenure' => $input['agreementTenure'],
            'latLng_id' => $location->id,
            'partner_id' => $input['partner_id'],
            'noOfBeds' => isset($input['noOfBeds'])?$input['noOfBeds']:0,
        ]);

        foreach ($input['rooms'] as $room){
            $data = [
                'capacity' => $room['capacity'],
                'noOfBeds' => $room['noOfBeds'],
                'price_per_bed' => $room['price_per_bed'],
                'room_number' => $room['room_number'],
                'nook_id' => $nook->id
            ];

            if(isset($room['id'])){
                $rm = Room::find($room['id']);
                if($rm){
                    $rm->update($data);
                }
            }else{
                Room::create($data);
            }

        }
        
        if($nook->space_type =="service" && $nook->status == Nook::$APPROVED){
            $approved_booking = Bookings::where("user_id",$nook->partner_id)->where('nook_id','!=',$nook->id)->where('status',Bookings::$APPROVED)->update([
                'status' => Bookings::$OFFBOARD
            ]);
            $checkBooking = Bookings::where('user_id',$nook->partner_id)->where('nook_id',$nook->id)->where('status',Bookings::$APPROVED)->first();
            if(!isset($checkBooking)){
                $booking = Bookings::create([
                    'status' => Bookings::$APPROVED,
                    'rent' => $input['rent'],
                    'security' => $input['security'],
                    'paidSecurity' => 0,
                    'user_id' => $nook->partner_id,
                    'nook_id' => $nook->id,
                    'room_id' => 0,
                ]);
            }
        }
        $nook = Nook::where('id',$id)->first();
        return [
            'message' => 'Nook updated successfully',
            'nook' => NooksResource::make($nook)
        ];
    }

}