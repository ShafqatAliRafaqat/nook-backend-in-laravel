<?php

namespace App\Http\Controllers\Auth;
use App\Helpers\NotificationsHelper;

use App\Bookings;
use App\UserDetails;
use App\Helpers\QB;
use App\Http\Resources\RoomShiftResource;
use App\Http\Resources\NooksResource;
use App\Nook;
use App\RoomShift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Validator;

class APIRoomShiftController extends Controller {

    public function index(Request $request){
       
        $input = $request->all();

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->with('medias')->first();
        
        $qb = RoomShift::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'nook' => NooksResource::make($nook),
            'data' => RoomShiftResource::collection($data)
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

        $shift = $user->roomshifts()->where('id',$input['id'])->first();

        if(!$shift){
            return abort(404,'Shift Not found.');
        }

        $shift->update([
            'status' => $input['status']
        ]);

        NotificationsHelper::SEND([
            'title' => 'Room Shift cancelled.',
            'body' => 'User Canceled room shift submitted on your nook ' . $shift->nook->nookCode,
        ],$shift->nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Room Shift updated successfully',
            'room_shift' => RoomShiftResource::make($shift)
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
        $totalShift = RoomShift::where('user_id',$user->id)->where('status',RoomShift::$PENDING)->count();
        
        if( $totalShift >= 1){
            abort(400,'You already have applied for Shift');
        }
        $room = $nook->rooms()->where('id',$input['room_id'])->first();

        if(!$room){
            abort(404,'Room Not found.');
        }
       
        $bookings = Bookings::where('room_id',$input['room_id'])->where('status',Bookings::$APPROVED)->count();

        if($bookings >= $room->capacity){

            abort(404,'Room Already Rented.');
        }

        $shift = RoomShift::create([
            'details' => $input['details'],
            'room_type' => $room->capacity,
            'status' => RoomShift::$PENDING,
            'price_per_bed' => $room->price_per_bed,
            'user_id' => $user->id,
            'nook_id' => $nook->id,
            'room_id' => $input['room_id']
        ]);

        NotificationsHelper::SEND([
            'title' => 'New Room Shift Request',
            'body' => 'New Room Shift Request added in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Room Shift is created successfully',
            'room_shift' => RoomShiftResource::make($shift)
        ];
    }
}
