<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\NotificationsHelper;
use App\Bookings;
use App\UserDetails;
use App\Helpers\QB;
use App\Http\Resources\BookingResource;
use App\Nook;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class APIBookingsController extends BaseController {

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();
        $qb = Bookings::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $bookings = $qb->get();

        return [
            'data' => BookingResource::collection($bookings)
        ];
    }
    
    public function cancel(Request $request){

        $user = Auth::user();

        $input = $request->all();
        
        $validator = Validator::make($input,[
            'booking_id' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $booking = Bookings::where('status',Bookings::$PENDING)
        ->where('user_id',$user->id)
        ->where('id',$input['booking_id'])
        ->first();

        if(!$booking){
            return abort(404,'Pending Booking does not exists in your account');
        }

        $booking->update([
            'status' => Bookings::$CANCELLED
        ]);


        return [
            'message' => 'Booking updated successfully',
            'booking' => BookingResource::make($booking)
        ];

    }

    public function add(Request $request){

        $user = Auth::user();
        
        if(!$user->userDetails->aggreedToTerms){
            return abort(400,'You can not create booking, please complete your profile first.');
        }
        
        if(!$user->userDetails->numberVerified){
            return abort(400,'You can not create booking, please verify your number.');
        }

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if($nook){
            return abort(400,'You are already registered with nook, please make shift request.');
        }

        $pendingBookings = Bookings::where('status',Bookings::$PENDING)->where('user_id',$user->id)->count();

        if($pendingBookings >= 2){
            return abort(400,'You are not allowed to submit bookings to more than two nooks at same time, please cancel your previous bookings');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'nook_id' => 'required',
            'room_id' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $pendingBookings = Bookings::where('status',Bookings::$PENDING)
        ->where('user_id',$user->id)
        ->where('nook_id',$input['nook_id'])
        ->count();


        if($pendingBookings >= 1){
            return abort(400,'You already booked this nook.');
        }

        $nook = Nook::where('id',$input['nook_id'])->first();

        if(!$nook){
            abort(404,'Nook Not found.');
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
            
            $rent = $room->price_per_bed;
            $security = ($nook->securityPercentage/100)*$rent;

        }else{
            
            $bookings = Bookings::where('nook_id',$input['nook_id'])->where('status',Bookings::$APPROVED)->first();  

            if($bookings){
                abort(404,'Nook Already Rented.');
            }

            $rent = $nook->rent;
            $security = $nook->security;
        }

        // booking should be gender restricted

        if($nook->gender_type !=='both' && (($nook->gender_type != $user->userDetails->gender))){
            return abort(404,'Because your gender is '.$user->userDetails->gender.' your are allowed to register in '.$nook->gender_type.' nook.');
        }

        NotificationsHelper::SEND([
            'title' => 'New Booking',
            'body' => 'New Booking is added in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        $booking = Bookings::create([
            'status' => Bookings::$PENDING,
            'rent' => (int)$rent,
            'security' => $security,
            'paidSecurity' => 0,
            'user_id' => $user->id,
            'nook_id' => $input['nook_id'],
            'room_id' => $input['room_id'],
        ]);


        return [
            'message' => 'Booking is created successfully',
            'booking' => BookingResource::make($booking)
        ];

    }
}
