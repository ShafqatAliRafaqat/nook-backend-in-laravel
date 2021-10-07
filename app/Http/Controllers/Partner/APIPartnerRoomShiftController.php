<?php

namespace App\Http\Controllers\Partner;

use App\Bookings;
use App\UserDetails;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\RoomShiftResource;
use App\Nook;
use App\RoomShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;

class APIPartnerRoomShiftController extends BaseController {

    protected $permissions = [
        // 'index'=>'shift-list',
        // 'edit' => 'shift-edit',
    ];

    public function index(Request $request){
        
        $user = Auth::user();
        
        $input = $request->all();

        $partnernooks = $user->partnernooks()->orderBy('updated_at','DESC')->get();
        
        $nook_id[] ='';
        
        foreach ($partnernooks as $data) {
            $nook_id[] = $data->id;
        }

        $qb = RoomShift::whereIn('nook_id',$nook_id)->orderBy('updated_at','DESC')->with(['user','nook']);

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::whereLike($input,"details",$qb);
        $qb = QB::where($input,"room_type",$qb);
        $qb = QB::where($input,"price_per_bed",$qb);
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

        $shifts = $qb->paginate(20);

        $shifts->appends(Input::except('page'));

        return RoomShiftResource::collection($shifts);
    }

    public function edit(Request $request,$id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'status' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $shift = RoomShift::where('id',$id)->first();

        if(!$shift){
            abort(404,'Room Shift not found.');
        }

        if($input['status'] === 'approved'){

    
            if($validator->fails()){
                abort(400,$validator->errors()->first());
            }

            // find users old booking and change status of it
            $booking = Bookings::where('user_id',$shift->user_id)
            ->where('status',Bookings::$APPROVED)->first();
            
            if ($booking) {

                $nook = Nook::where('id',$shift->nook_id)->first();

                if(!$nook){
                    abort(404,'Nook Not found.');
                }
        
                $room = $nook->rooms()->where('id',$shift->room_id)->first();
        
                if(!$room){
                    abort(404,'Room Not found.');
                }

                $rent = $room->price_per_bed;
                $security = ($nook->securityPercentage/100)*$rent;

                $user = UserDetails::where('user_id',$shift->user_id)->first();
        
                if($user){
                    $user->update([
                        'room_id'=> $shift->room_id,
                        'nook_id'=> $shift->nook_id,
                    ]);
                }
               
               $booking->update([
                    'rent' => $rent,
                    'security' => $security,
                    'nook_id' => $shift->nook_id,
                    'room_id' => $shift->room_id,
                ]); 
            }

        }

        if($input['status'] != $shift->status){
            NotificationsHelper::SEND([
                'title' => 'Room Shift Updated',
                'body' => 'Room Shift status updated to ' . $input['status'],
            ],$shift->user_id, env("APP_ID"));
        }
        
        RoomShift::where('id',$id)->update([
            'status' => $input['status']
        ]);

        return [
            'message' => 'Room Shift Updated Successfully',
            'shift' => RoomShiftResource::make($shift),
        ];
    }
}
