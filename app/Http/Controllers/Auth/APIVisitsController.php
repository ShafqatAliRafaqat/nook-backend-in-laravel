<?php
namespace App\Http\Controllers\Auth;

use App\Bookings;
use App\Helpers\QB;
use App\Http\Resources\VisitResource;
use App\Nook;
use App\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;

class APIVisitsController extends Controller {

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();
        $qb = Visit::where('user_id',$user->id)->with(['user','nook','partner'])->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $visits = $qb->get();

        return [
            'data' => VisitResource::collection($visits)
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
            abort(400,$validator->errors()->first());
        }

        $visit = $user->visits()->where('id',$input['id'])->first();

        if(!$visit){
            return abort(404,'Visit Not found.');
        }

        $visit->update([
            'status' => $input['status']
        ]);

        NotificationsHelper::SEND([
            'title' => 'Nook visit cancelled.',
            'body' => 'User Canceled visit on your nook ' . $visit->nook->nookCode,
        ],$visit->nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Visit updated successfully',
            'visit' => VisitResource::make($visit)
        ];
    }

    public function add(Request $request){
        $user = Auth::user();

        if($user->userDetails->aggreedToTerms == null){
            return abort(400,'You can not create Visit, please complete your profile first.');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'nook_id' => 'required',
            'time' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $nook = Nook::where('id',$input['nook_id'])->first();

        if(!$nook){
            abort(404,'Nook Not found.');
        }

        $time = Carbon::createFromTimestamp($input['time']);
        if($time->isPast()){
            abort(400,'Select upcoming date time to schedule visit');
        }
        $visit = Visit::where('user_id',$user->id)->where('start','<=',$time)->where('end','>=',$time)->first();

        if($visit){
            abort(400,'You already have visit in this timeslot');
        }

        $visit = Visit::where('partner_id',$nook->partner_id)->where('start','<=',$time)->where('end','>=',$time)->first();

        if($visit){
            abort(400,'Nook is already booked in this timeslot');
        }
       
        $totalVisit = Visit::where('user_id',$user->id)->where('status',Visit::$PENDING)->count();
        
        if( $totalVisit >= 2){
            abort(400,'You already have booked two visit');
        }
        
        $visit = Visit::create([
            'status' => Visit::$PENDING,
            'start' => $time,
            'end' => Carbon::createFromTimestamp($input['time'])->addMinutes(30),
            'partner_id' => $nook->partner_id,
            'user_id' => $user->id,
            'nook_id' => $input['nook_id'],
        ]);

        NotificationsHelper::SEND([
            'title' => 'New Visit',
            'body' => 'New Visit added in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Visit is created successfully',
            'visit' => VisitResource::make($visit)
        ];

    }
}
