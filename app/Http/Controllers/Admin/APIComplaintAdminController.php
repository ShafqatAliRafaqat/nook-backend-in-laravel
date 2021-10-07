<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Helpers\NotificationsHelper;
use App\Media;
use App\Complaint;
use App\Bookings;
use App\Helpers\FileHelper;
use App\Helpers\QB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ComplaintResource;
use App\Nook;
use App\User;

class APIComplaintAdminController extends BaseController {


    protected $permissions = [
        'index'=>'complaint-list-all',
    ];

    public function index(Request $request){
        $input = $request->all();

        $qb = Complaint::orderBy('updated_at','DESC')->with(['user','nook']);

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
    public function add(Request $request){
        
        $input = $request->all();
        
        $user = User::where('id',$input['user_id'])->with(['nooks'])->first();
        if(!$user){
            return abort(400,'Select User');
        }
        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();
        
        if(!$nook){
            return abort(400,'User is not registered in any nook');
        }
        $booking = Bookings::where('user_id',$user->id)->where('nook_id',$nook->id)->where('status',Bookings::$APPROVED)->first();
        
        if(!$nook || !$booking){
            return abort(400,'You are not registered in any nook');
        }

        $validator = Validator::make($input,[
            'description' => 'required',
            'type'        => 'required',
            'status'      => 'required',
            'user_id'     => 'required',
            // 'partner_id'  => 'required',
            // 'media'       => 'nullable|mimes:jpeg,jpg,png',
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
            'status' => $input['status'],
            'user_id' => $input['user_id'],
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
    public function edit(Request $request,$id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'status' => 'required',
            'type'   => 'required',
            'description'=> 'required',
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
            'status' => $input['status'],
            'type' => $input['type'],
            'description' => $input['description']
        ]);

        return [
            'message' => 'Complain Updated Successfully',
            'complain' => ComplaintResource::make($complaint),
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
