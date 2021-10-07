<?php

namespace App\Http\Controllers\Auth;
use App\Helpers\NotificationsHelper;

use App\Media;
use App\Complaint;
use App\Bookings;
use App\Helpers\QB;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ComplaintResource;

class APIComplainsController extends Controller {

    public function index(Request $request){
        $input = $request->all();
        $user = Auth::user();

        $qb = Complaint::where('user_id',$user->id)->whereNull('to_user_id')->orderBy('updated_at','DESC');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"type",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $data = $qb->get();

        return [
            'data' => ComplaintResource::collection($data)
        ];
    }

    public function add(Request $request){

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        $booking = Bookings::where('user_id',$user->id)->where('nook_id',$nook->id)->where('status',Bookings::$APPROVED)->first();
        
        if(!$nook || !$booking){
            return abort(400,'You are not registered in any nook');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'description' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        
        $media_id = 0;

        if($request->media){
            $img = Image::make($request->media);
            $result = FileHelper::getAndCreatePath($img->filename,'complains');
            $extension = substr($img->mime,strpos($img->mime,'/')+1);
            $result['name'] = $result['name'].str_random(15).'.'.$extension;

            $path   = $this->saveImage($img,'default',1,$result);
            $medium = $this->saveImage($img,'medium',0.75,$result);
            $small  = $this->saveImage($img,'small',0.40,$result);

            $media = Media::create([
                'name' => $result['name'],
                'path' => $path,
                'small' => $small,
                'medium' => $medium,
            ]);
            $media_id = $media->id;
        }

        $complain = Complaint::create([
            'description' => $input['description'],
            'type' => $input['type'],
            'status' => Complaint::$PENDING,
            'user_id' => $user->id,
            'nook_id' => $nook->id,
            'room_id' => $booking->room_id,
            'media_id' => $media_id,
        ]);

        NotificationsHelper::SEND([
            'title' => 'New Complain',
            'body' => 'New Complain submitted in your nook ' . $nook->nookCode,
        ],$nook->partner_id, env("PARTNER_APP_ID"));

        return [
            'message' => 'Complain is created successfully',
            'complain' => ComplaintResource::make($complain)
        ];
    }
    private function saveImage($img,string $prefix, float $percentage,array $result){

        $img->resize($img->width()*$percentage, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $path = $result['path']."/$prefix-".$result['name'];
        $img->save($path, 60);

        return $path;
    }

}
