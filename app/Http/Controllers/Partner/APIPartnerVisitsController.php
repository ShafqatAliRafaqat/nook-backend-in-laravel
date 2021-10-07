<?php

namespace App\Http\Controllers\Partner;

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

class APIPartnerVisitsController extends Controller {

    protected $permissions = [
        // 'index'=>'visits-list',
        // 'update'=>'visits-edit'
    ];

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();
        $qb = Visit::where('partner_id',$user->id)->with(['user','nook','partner'])->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $visits = $qb->get();

        return [
            'data' => VisitResource::collection($visits)
        ];
    }

    public function update(Request $request, $id){
        
        $user = Auth::user();

        $input = $request->all();

        $validator = Validator::make($input,[
            'status' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $visit = Visit::where('id',$id)->first();

        if(!$visit){
            return abort(404,'Visit Not found.');
        }

        if($input['status'] != $visit->status){
            NotificationsHelper::SEND([
                'title' => 'Visit Updated',
                'body' => 'Visit status updated to ' . $input['status'],
            ],$visit->user_id, env("APP_ID"));
        }
        
        $visit->update([
            'status' => $input['status']
        ]);

        return [
            'message' => 'Visit updated successfully',
            'visit' => VisitResource::make($visit)
        ];
    }
    
}
