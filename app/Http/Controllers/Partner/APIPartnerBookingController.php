<?php
namespace App\Http\Controllers\Partner;

use App\Helpers\NotificationsHelper;
use App\Bookings;
use App\UserDetails;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\BookingResource;
// use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class APIPartnerBookingController extends BaseController {

    protected $permissions = [
        // 'index'=>'bookings-list',
        // 'update'=>'bookings-update',
        // 'addSecurity' => 'bookings-addSecurity'
    ];

    public function index(Request $request){

        $user = Auth::user();

        $input = $request->all();
        
        $partnernooks = $user->partnernooks()->orderBy('updated_at','DESC')->get();
        
        $nook_id[] ='';
        
        foreach ($partnernooks as $data) {
            $nook_id[] = $data->id;
        }
        
        $qb = Bookings::whereIn('nook_id',$nook_id)->orderBy('updated_at','DESC')->with(['user','nook', 'receipts' => function ($query) {
            $query->where('status', 'draft');
        }]);

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
            ],$booking->user_id, env("APP_ID"));
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
        ],$booking->user_id, env("APP_ID"));

        return [
            'message' => 'Security Updated Successfully',
            'booking' => BookingResource::make($booking)
        ];
    }

}
