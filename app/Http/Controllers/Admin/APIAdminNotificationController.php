<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\NotificationsHelper;
use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\NotificationResource;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class APIAdminNotificationController extends BaseController {

    protected $permissions = [
        'index'=>'notification-list-all',
        'save'=>'notification-create'
    ];

    public function index(Request $request){
        $input = $request->all();

        $qb = Notification::orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"user_id",$qb);
        $qb = QB::whereLike($input,"title",$qb);
        $qb = QB::whereLike($input,"body",$qb);

        $nooks = $qb->paginate(20);

        $nooks->appends(Input::except('page'));

        return NotificationResource::collection($nooks);
    }

    public function add(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'title' => 'required',
            'body' => 'required',
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $noti = NotificationsHelper::SEND([
            'title' => $input['title'],
            'body' => $input['body'],
        ],$input['user_id'], env("APP_ID"));

        return [
            'message' => 'Notification Created Successfully',
            'data' =>  NotificationResource::make($noti),
        ];
    }
}
