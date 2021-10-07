<?php

namespace App\Http\Controllers\Admin;

use App\Bookings;
use App\UserDetails;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\BookingResource;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;
use App\Nook;
use App\User;

class APIBookingAdminController extends BaseController {

    protected $permissions = [
        'index'=>'bookings-list-all',
        'save'=>'bookings-create',
        'edit' => 'bookings-edit',
        'delete' => 'bookings-delete'
    ];

    public function index(Request $request){
        $input = $request->all();

        $qb = Bookings::orderBy('updated_at','DESC')->with(['user','nook']);

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $qb = $qb->whereHas('nook', function ($q) use($request) {
            if ($request->space_type) {
                $q->where('space_type', $request->space_type);
            }
            if ($request->nookCode) {
                $q->where('nookCode', $request->nookCode);
            }
        });

        $qb = $qb->whereHas('user', function ($q) use($request) {
            if ($request->number) {
                $q->where('number', $request->number);
            }
            if ($request->email) {
                $q->where('email', $request->email);
            }
        });

        $bookings = $qb->paginate(20);

        $bookings->appends(Input::except('page'));

        return BookingResource::collection($bookings);
    }
    public function add(Request $request){

        $input = $request->all();
        $validator = Validator::make($input,[
            'nook_id' => 'required',
            'room_id' => 'required',
            'user_id' => 'required',
            'status'  => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        $user = User::where('id',$input['user_id'])->with(['nooks'])->first();
        
        if(!$user){
            return abort(400,'Select User');
        }

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if($nook){
            return abort(400,'User is already registered with nook, please make shift request.');
        }

        $pendingBookings = Bookings::where('status',Bookings::$PENDING)->where('user_id',$user->id)->count();

        if($pendingBookings >= 2){
            return abort(400,'User is not allowed to submit bookings to more than two nooks at same time, please cancel your previous bookings');
        }

        $pendingBookings = Bookings::where('status',Bookings::$PENDING)
        ->where('user_id',$user->id)
        ->where('nook_id',$input['nook_id'])
        ->count();


        if($pendingBookings >= 1){
            return abort(400,'User already booked this nook.');
        }

        $nook = Nook::where('id',$input['nook_id'])->with('rooms')->first();

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
        if($user->userDetails->gender){
            if($nook->gender_type !=='both' && (($nook->gender_type != $user->userDetails->gender))){
                return abort(404,'Because your gender is '.$user->userDetails->gender.' your are allowed to register in '.$nook->gender_type.' nook.');
            }
        }

        NotificationsHelper::SEND([
            'title' => 'New Booking',
            'body' => 'New Booking is added in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        $booking = Bookings::create([
            'status' => $input['status'],
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
    public function update(Request $request, $id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'status' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $booking = Bookings::findOrFail($id);

        if($input['status'] == Bookings::$OFFBOARD){

            $validator = Validator::make($input,[
                'refunedSecurity' => 'required',
            ]);
    
            if($validator->fails()){
                abort(400,$validator->errors()->first());
            }

            $booking->update([
                'status' => $input['status'],
                'refunedSecurity' => $input['refunedSecurity']
            ]);
            
            return [
                'message' => 'Booking Updated Successfully',
                'booking' => BookingResource::make($booking)
            ];
        }

        if($input['status'] != Bookings::$APPROVED){
            $booking->update([
                'status' => $input['status']
            ]);
            return [
                'message' => 'Booking Updated Successfully',
                'booking' => BookingResource::make($booking)
            ];
        }

        $validator = Validator::make($input,[
            'installments' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $paidSecurity = $booking->security / $input['installments'];

        if($input['status'] != $booking->status){
            NotificationsHelper::SEND([
                'title' => 'Booking Updated',
                'body' => 'Booking status updated to ' . $input['status'],
            ],$booking->user_id,env("APP_ID"));
        }
        
        $booking->update([
            'status' => $input['status'],
            'installments' => $input['installments'],
            'paidSecurity' => $paidSecurity,
        ]);
        
        $user = UserDetails::where('user_id',$booking->user_id)->first();
        
        if($user){
            
            $user->update([
                'room_id'=> $booking->room_id,
                'nook_id'=> $booking->nook_id,
            ]);
        }
        
        return [
            'message' => 'Booking Updated Successfully',
            'booking' => BookingResource::make($booking)
        ];
    }
    public function addSecurity(Request $request, $id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'security' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $booking = Bookings::findOrFail($id);

        $booking->update([
            'paidSecurity' => $booking->paidSecurity + $input['security'],
        ]);

        NotificationsHelper::SEND([
            'title' => 'Security Added',
            'body' => $input['security']. ' security added in your account.',
        ],$booking->user_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Security Updated Successfully',
            'booking' => BookingResource::make($booking)
        ];
    }

}
