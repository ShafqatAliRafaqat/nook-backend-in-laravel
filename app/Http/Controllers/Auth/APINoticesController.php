<?php
namespace App\Http\Controllers\Auth;
use App\Helpers\NotificationsHelper;

use App\Helpers\QB;
use App\Notice;
use App\Bookings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Resources\NoticeResource;
use App\Setting;
use Illuminate\Support\Facades\Validator;


class APINoticesController extends Controller{

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();

        $qb = Notice::where('user_id',$user->id)->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'data' => NoticeResource::collection($data)
        ];
    }


    public function cancel(Request $request){

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if(!$nook){
            return abort(404,'You are not registered in any nook');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'notice_id' => 'required',
        ]);

        if($validator->fails()){
            return abort(400,$validator->errors()->first());
        }

        $notice = Notice::findOrFail($input['notice_id']);
        $cDay = $notice->checkout->format('j');
        $currentDay = Carbon::now()->format('j');
        $daysDiff = $cDay - $currentDay;

        $noticeDays = Setting::getValue('user_can_cancel_notice');

        if($daysDiff < $noticeDays){
            return abort(400,'Your are not allowed to cancel your notice at this time');
        }

        $notice->update([
            'status' => Notice::$CANCELLED
        ]);

        NotificationsHelper::SEND([
            'title' => 'Notice Canceled',
            'body' => 'User Canceled notice submitted on your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Notice updated successfully',
            'notice' => NoticeResource::make($notice)
        ];

    }

    public function add(Request $request){

        $noticeDays = Setting::getValue('user_can_give_notice');
        $currentDay = Carbon::now()->format('j');

        if($noticeDays < $currentDay){
            return abort(400,'You can submit notice before '.$noticeDays.' Of every month.');
        }

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if(!$nook){
            return abort(404,'You are not registered in any nook');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'details' => 'required',
            'checkout' => 'required',
        ]);

        $currentMonth = Carbon::now()->format('n');

        $notice = Notice::where('month',$currentMonth)
        ->where('user_id',$user->id)
        ->where('nook_id',$nook->id)
        ->where('month',$currentMonth)
        ->where('status',Notice::$PENDING)
        ->first();

        if($notice){
            return abort(400,'You already have pending notice.');
        }

        $notice1 = Notice::where('month',$currentMonth - 1)
        ->where('user_id',$user->id)
        ->where('status',Notice::$APPROVED)
        ->first();
        
        $notice2 = Notice::where('month',$currentMonth - 2)
        ->where('user_id',$user->id)
        ->where('status',Notice::$APPROVED)
        ->first();

        if($notice1 && $notice2){
            return abort(400,'You Cannot give this month notice try next month');
        }


        if($validator->fails()){
            return abort(400,$validator->errors()->first());
        }
        
        $notice = Notice::create([
            'details' => $input['details'],
            'checkout' => Carbon::createFromTimestamp($input['checkout']),
            'month' => $currentMonth,
            'status' => Notice::$PENDING,
            'user_id' => $user->id,
            'nook_id' => $nook->id,
        ]);

        NotificationsHelper::SEND([
            'title' => 'New Notice',
            'body' => 'New Notice added in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Notice is created successfully',
            'notice' => NoticeResource::make($notice)
        ];
    }
}
