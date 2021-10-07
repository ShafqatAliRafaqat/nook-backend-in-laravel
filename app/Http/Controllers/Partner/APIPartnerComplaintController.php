<?php

namespace App\Http\Controllers\Partner;

use App\User;
use App\Media;
use App\Bookings;
use App\Complaint;
use App\Helpers\QB;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotificationsHelper;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\BaseController;
use App\Http\Resources\ComplaintResource;
use Illuminate\Support\Facades\Validator;

class APIPartnerComplaintController extends BaseController {


    protected $permissions = [
        // 'index'=>'complaint-list',
        // 'edit'=>'complaint-edit',
    ];

    public function index(Request $request){
        
        $user = Auth::user();
        
        $input = $request->all();

        $partnernooks = $user->partnernooks()->orderBy('updated_at','DESC')->get();
        
        $nook_id[] ='';
        
        foreach ($partnernooks as $data) {
            $nook_id[] = $data->id;
        }
        $qb = Complaint::whereIn('nook_id',$nook_id)->whereNull('to_user_id')->orderBy('updated_at','DESC')->with(['user','nook']);

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"description",$qb);
        $qb = QB::where($input,"type",$qb);
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

        $complaints = $qb->paginate(20);

        $complaints->appends(Input::except('page'));

        return ComplaintResource::collection($complaints);
    }


    public function edit(Request $request,$id){

        $input = $request->all();
        
        $validator = Validator::make($input,[
            'status' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $complaint = Complaint::where('id',$id)->first();

        if(!$complaint){
            abort(400,__('messages.complaint.not.found'));
        }

        if($input['status'] != $complaint->status){
            NotificationsHelper::SEND([
                'title' => 'Complain Updated',
                'body' => 'Complain status updated to ' . $input['status'],
            ],$complaint->user_id, env("APP_ID"));
        }
        
        $complaint->update([
            'status' => $input['status']
        ]);

        return [
            'message' => 'Complain Updated Successfully',
            'complain' => ComplaintResource::make($complaint),
        ];
    }
    public function add(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'description' => 'required',
            'type'        => 'required',
            'user_id'     => 'required',
        ]);

        $partner = Auth::user();
        
        $user = User::where("id",$input['user_id'])->first();
        
        if(!$user){
            return abort(400,'User not found');
        }
        
        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        $booking = Bookings::where('user_id',$user->id)->where('nook_id',$nook->id)->where('status',Bookings::$APPROVED)->first();
        
        if(!$nook || !$booking){
            return abort(400,'User is not registered in this nook');
        }

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
            'to_user_id' => $partner->id,
            'nook_id' => $nook->id,
            'room_id' => $booking->room_id,
            'user_id' => $input['user_id'],
            'media_id' => $media_id,
        ]);
        return [
            'message' => 'Complain is created successfully',
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
