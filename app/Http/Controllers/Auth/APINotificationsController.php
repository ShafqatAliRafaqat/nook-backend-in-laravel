<?php

namespace App\Http\Controllers\Auth;
use App\Helpers\NotificationsHelper;

use App\Media;
use App\Notification;
use App\Bookings;
use App\Helpers\QB;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Resources\NotificationResource;

class APINotificationsController extends Controller {

    public function index(Request $request){

        $user = Auth::user();

        $input = $request->all();

        $qb = Notification::orderBy('updated_at','DESC')->where('user_id', $user->id);

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"title",$qb);
        $qb = QB::whereLike($input,"body",$qb);

        $nooks = $qb->get();

        return NotificationResource::collection($nooks);
    }

}
