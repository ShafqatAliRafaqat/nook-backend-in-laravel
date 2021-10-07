<?php

namespace App\Http\Controllers\Partner;

use App\Complaint;
use App\Helpers\QB;
use App\Helpers\FileHelper;
use App\Http\Controllers\BaseController;
use App\Http\Resources\NooksResource;
use App\Http\Resources\AreaResource;
use App\LatLng;
use App\Nook;
use App\Room;
use App\Review;
use App\Area;
use App\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\Location;
use Illuminate\Support\Facades\Auth;

class APIPartnerNookController extends BaseController {
    protected $permissions = [
        // 'index'=>'nook-list',
        // 'area'=>'nook-area',
        // 'add'=>'nook-create',
        // 'delete'=>'nook-delete'
    ];

    public function index(Request $request){
        
        $input = $request->all();
        $user = Auth::user();

        $qb = $user->partnernooks()->orderBy('updated_at','DESC')->with('medias');

        $qb = QB::where($input,"id",$qb);
        $qb = QB::whereLike($input,"description",$qb);
        $qb = QB::whereLike($input,"facilities",$qb);
        $qb = QB::where($input,"type",$qb);
        $qb = QB::where($input,"space_type",$qb);
        $qb = QB::where($input,"gender_type",$qb);
        $qb = QB::where($input,"nookCode",$qb);
        $qb = QB::where($input,"status",$qb);
        $qb = QB::where($input,"nook_id",$qb);

        $nooks = $qb->paginate(20);

        $nooks->appends(Input::except('page'));

        return NooksResource::collection($nooks);
    }

    public function area() {
        $area = Area::all();
        return AreaResource::collection($area);
    }

    public function add(Request $request){

        $input = $request->all();

        $user = Auth::user();

        $validator = Validator::make($input,[
            'type' => 'required',
            'space_type' => 'required',
            'gender_type' => 'required',
            'number'        => 'required',
            'description' => 'present',
            'facilities' => 'present',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
        if ($request->lat && $request->lng) {
            $location = LatLng::create([
                'lat' => $input['lat'],
                'lng' => $input['lng'],
            ]);    
        }


        if(!$user->userDetails->aggreedToTerms){
            return abort(400,'You can not add nook, please complete your profile first.');
        }

        if(!$user->userDetails->numberVerified){
            return abort(400,'You can not add nook, please verify your number.');
        }

        $nook = Nook::create([
            'type' => $input['type'],
            'space_type' => $input['space_type'],
            'gender_type' => $input['gender_type'],
            'number' => $input['number'],
            'description' => $input['description'],
            'status' => Nook::$PENDING,
            'partner_id' => $user->id,
            'latLng_id' => isset($location)?$location->id:null,
            'facilities' => json_encode($input['facilities']),
            'address' => $input['address'],
            'area' => $input['independent_area'],
            'area_unit' => $input['area_unit'],
            'inner_details' => $input['inner_details'],
            'other' => $input['other'],
            'furnished' => $input['furnished'],
            'rent' => $input['rent'],
            'security' => $input['security'],
            'agreementCharges' => $input['agreementCharges'],
            'agreementTenure' => $input['agreementTenure'],
        ]);
       
        foreach ($input['rooms'] as $room){
            Room::create([
                'capacity' => $room['capacity'],
                'noOfBeds' => $room['noOfBeds'],
                'price_per_bed' => $room['price_per_bed'],
                'room_number' => $room['room_number'],
                'nook_id' => $nook->id
            ]);
        }

        if ($request->review) {

            Review::create([
                "ratting" => $request->review,
                "user_id" => $user->id,
                "nook_id" => $nook->id
            ]);
            
        }
        if(count($request->images)>0){
            
            foreach ($request->images as $image) {
                
                $paths = $this->saveImages($image);

                $media = Media::create([
                    'name' => $paths['name'],
                    'caption' => $paths['name'],
                    'alt' => $paths['name'],
                    'nook_id' => $nook->id,
                    'path' => $paths['path'],
                    'small' => $paths['small'],
                    'medium' => $paths['medium'],
                ]);   
            }
        }

        return [
            'message' => 'Nook created successfully',
            'nook' => NooksResource::make($nook)
        ];
    }

    public function edit(Request $request, $id){

        $input = $request->all();
        
        $user = Auth::user();
        
        $validator = Validator::make($input,[
            'type' => 'required',
            'space_type' => 'required',
            'gender_type' => 'required',
            'description' => 'present',
            'facilities' => 'present',
            'number' => 'required',
            'address' => 'required',
            'area' => 'present',
            'area_unit' => 'present',
            'inner_details' => 'present',
            'other' => 'present',
            'furnished' => 'present',
            'rent' => 'present',
            'security' => 'present',
            'agreementCharges' => 'present',
            'agreementTenure' => 'present',
            'lat' => 'present',
            'lng' => 'present',
            'rooms' => 'present',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $nook = Nook::findOrFail($id);

        $location = LatLng::find($nook->latLng_id);

        if($location){
            $location->update([
                'lat' => $input['lat'],
                'lng' => $input['lng'],
            ]);
        }else{
            $location = LatLng::create([
                'lat' => $input['lat'],
                'lng' => $input['lng'],
            ]);
        }

        $nook->update([
            'type' => $input['type'],
            'space_type' => $input['space_type'],
            'gender_type' => $input['gender_type'],
            'description' => $input['description'],
            'facilities' => json_encode($input['facilities']),
            'number' => $input['number'],
            'address' => $input['address'],
            'area' => $input['area'],
            'area_unit' => $input['area_unit'],
            'inner_details' => $input['inner_details'],
            'other' => $input['other'],
            'furnished' => $input['furnished'],
            'rent' => $input['rent'],
            'security' => $input['security'],
            'agreementCharges' => $input['agreementCharges'],
            'agreementTenure' => $input['agreementTenure'],
            'latLng_id' => $location->id,
        ]);

        foreach ($input['rooms'] as $room){
            $data = [
                'capacity' => $room['capacity'],
                'noOfBeds' => $room['noOfBeds'],
                'price_per_bed' => $room['price_per_bed'],
                'room_number' => $room['room_number'],
                'nook_id' => $nook->id
            ];

            if(isset($room['id'])){
                $rm = Room::find($room['id']);
                if($rm){
                    $rm->update($data);
                }
            }else{
                Room::create($data);
            }

        }
        if ($request->review) {
            
            $review = Review::where('nook_id',$nook->id)->where('user_id',$user->id)->first();
            
            if($review){
                $review->update([
                    "ratting" => $request->review,
                ]);
            }else{
                Review::create([
                    "ratting" => $request->review,
                    'nook_id' => $nook->id,
                    'user_id' => $user->id
                ]);
            }
        }

        $medias = Media::whereNotIn('id',$input['media_ids'])->where('nook_id',$id)->get();
                
        foreach($medias as $media){
            $this->deleteImages($media);
            $media->delete();
        }

        if(count($request->images)>0){
               
            foreach ($request->images as $image) {
                
                $paths = $this->saveImages($image);

                $media = Media::create([
                    'name' => $paths['name'],
                    'caption' => $paths['name'],
                    'alt' => $paths['name'],
                    'nook_id' => $nook->id,
                    'path' => $paths['path'],
                    'small' => $paths['small'],
                    'medium' => $paths['medium'],
                ]);   
            }
        }
        return [
            'message' => 'Nook updated successfully',
            'nook'    => NooksResource::make($nook)
        ];
    }

    // private function
    private function saveImages($image){

        $img = Image::make($image);

        $result = FileHelper::getAndCreatePath($img->filename, 'medias');

        $extension = substr($img->mime,strpos($img->mime,'/')+1);

        $result['name'] = $result['name'].str_random(15).'.'.$extension;

        $path = $this->saveImage($img,'default',1,$result);
        $medium = $this->saveImage($img,'medium',0.75,$result);
        $small = $this->saveImage($img,'small',0.40,$result);

        return [
            'path' => $path,
            'medium' => $medium,
            'small' => $small,
            'name' => $result['name'],
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

    private function deleteImages($media){
        FileHelper::deleteFileIfNotDefault($media->small);
        FileHelper::deleteFileIfNotDefault($media->medium);
        FileHelper::deleteFileIfNotDefault($media->path);
    }

    public function delete($id){
        $nook = Nook::where('id',$id)->first();
        $nook->delete();
        return ['message'=>'Nook deleted successfully'];
    }
    
    private function getMedia($id,$p){
        return Media::where('id',$id)->first();
    }

}