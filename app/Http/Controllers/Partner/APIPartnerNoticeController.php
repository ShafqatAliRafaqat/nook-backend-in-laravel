<?php

namespace App\Http\Controllers\Partner;

use App\Bookings;
use App\Complaint;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\NoticeResource;
use App\LatLng;
use App\Nook;
use App\Notice;
use App\Room;
use App\Transaction;
use App\User;
use App\UserDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\NotificationsHelper;

class APIPartnerNoticeController extends BaseController
{

    protected $permissions = [
        // 'index' => 'notice-list',
        // 'edit' => 'notice-edit',
    ];

    public function index(Request $request){
        
        $user = Auth::user();
        
        $input = $request->all();

        $partnernooks = $user->partnernooks()->orderBy('updated_at','DESC')->get();
        
        $nook_id[] ='';
        
        foreach ($partnernooks as $data) {
            $nook_id[] = $data->id;
        }
        $qb = Notice::whereIn('nook_id',$nook_id)->orderBy('updated_at', 'DESC')->with(['user', 'nook']);

        $qb = QB::where($input, "id", $qb);
        $qb = QB::where($input, "status", $qb);
        $qb = QB::whereLike($input, "details", $qb);
        $qb = QB::where($input, "user_id", $qb);
        $qb = QB::where($input, "nook_id", $qb);


        $qb = $qb->whereHas('nook', function ($q) use ($request) {
            if ($request->space_type) {
                $q->where('space_type', $request->space_type);
            }
            if ($request->nookCode) {
                $q->where('nookCode', $request->nookCode);
            }
        });

        $qb = $qb->whereHas('user', function ($q) use ($request) {
            if ($request->number) {
                $q->where('number', $request->number);
            }
            if ($request->email) {
                $q->where('email', $request->email);
            }
        });

        $notices = $qb->paginate(20);

        $notices->appends(Input::except('page'));

        return NoticeResource::collection($notices);
    }

    public function edit(Request $request, $id)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            abort(400, $validator->errors()->first());
        }

        $notice = Notice::where('id', $id)->first();

        if (!$notice) {
            abort(404, 'Notice not found.');
        }

        if($input['status'] != $notice->status){
            NotificationsHelper::SEND([
                'title' => 'Notice Updated',
                'body' => 'Notice status updated to ' . $input['status'],
            ],$notice->user_id, env("APP_ID"));
        }
        
        $notice->update([
            'status' => $input['status']
        ]);

        return [
            'message' => 'Notice Updated Successfully',
            'notice' => NoticeResource::make($notice),
        ];
    }
}
