<?php

namespace App\Http\Controllers\Auth;
use App\Helpers\NotificationsHelper;

use App\Bookings;
use App\Helpers\QB;
use App\Http\Resources\ShiftResource;
use App\Nook;
use App\Shift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class APIShiftController extends Controller {

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();

        $qb = Shift::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'data' => ShiftResource::collection($data)
        ];
    }

    public function cancel(Request $request){
        
        $user = Auth::user();

        $input = $request->all();

        $validator = Validator::make($input,[
            'id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return abort(400,$validator->errors()->first());
        }

        $shift = $user->shifts()->where('id',$input['id'])->first();

        if(!$shift){
            return abort(404,'Shift Not found.');
        }

        $shift->update([
            'status' => $input['status']
        ]);

        NotificationsHelper::SEND([
            'title' => 'Nook Shift Request Canceled',
            'body' => 'User Canceled Nook shift request on your nook ' . $shift->nook->nookCode,
        ],$shift->nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Shift updated successfully',
            'shift' => ShiftResource::make($shift)
        ];
    }

    public function add(Request $request){

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if(!$nook){
            return abort(400,'You are not registered in any nook, please make book request');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'details' => 'required',
            'nook_id' => 'required',
            'room_id' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $nook = Nook::where('id',$input['nook_id'])->first();

        if(!$nook){
            abort(404,'Desired Nook Not found.');
        }
        $totalShift = Shift::where('user_id',$user->id)->where('status',Shift::$PENDING)->count();
        
        if( $totalShift >= 1){
            abort(400,'You already have applied for Shift');
        }
        
        if($input['room_id'] != 0){
        
            $room = $nook->rooms()->where('id',$input['room_id'])->first();

            if(!$room){
                abort(404,'Room Not found.');
            }
            
            $bookings = Bookings::where('room_id',$input['room_id'])->where('status',Bookings::$APPROVED)->count();  

            if($bookings >= $room->capacity){

                abort(404,'Room Already Rented.');
            }
            
            $capacity = $room->capacity;
            $price_per_bed = $room->price_per_bed;

        }else{
            
            $bookings = Bookings::where('nook_id',$input['nook_id'])->where('status',Bookings::$APPROVED)->first();  

            if($bookings){
                abort(404,'Nook Already Rented.');
            }
             
            $capacity = 0;
            $price_per_bed = 0;
        }

        $shift = Shift::create([
            'details' => $input['details'],
            'room_type' => $capacity,
            'status' => Shift::$PENDING,
            'price_per_bed' => $price_per_bed,
            'user_id' => $user->id,
            'nook_id' => $nook->id,
            'room_id' => $input['room_id']
        ]);


        NotificationsHelper::SEND([
            'title' => 'New Nook Shift Request',
            'body' => 'User added new nook shift request on your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Shift is created successfully',
            'shift' => ShiftResource::make($shift)
        ];
    }
}
