<?php

namespace App\Http\Controllers\Auth;

use App\Bookings;
use App\Http\Resources\NooksResource;
use App\Http\Resources\RoomResource;
use App\Http\Resources\ReviewResource;
use App\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Complaint;
use App\Helpers\QB;
use App\Helpers\FileHelper;
use App\Http\Resources\AreaResource;
use App\LatLng;
use App\Nook;
use App\Room;
use App\Area;
use App\Media;
use Illuminate\Support\Facades\Input;
use Intervention\Image\Facades\Image;
use phpDocumentor\Reflection\Location;

class APINookController extends BaseController{
    
    public function nook(){
        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();
        
        if($nook){
            $bookings = $nook->bookings()->first();
            $room = $bookings->room()->first();
        }

        if(!$nook){
            return abort(404,'Nook not found !');
        }

        $review = $user->reviews()->where('nook_id',$nook->id)->first();

        return [
            'nook' => NooksResource::make($nook),
            'review' => $review ? ReviewResource::make($review): null,
            'room' => isset($room) ? RoomResource::make($room): null,
        ];
    }

    public function addReview(Request $request) {

        $user = Auth::user();

        $nook = $user->nooks()->where('bookings.status',Bookings::$APPROVED)->first();

        if(!$nook){
            return abort(404,'Nook not found !');
        }

        $input = $request->all();

        $validator = Validator::make($input,[
            'rating' => 'required',
        ]);


        if($validator->fails()){
            return abort(400,$validator->errors()->first());
        }

        $review = Review::where('user_id',Auth::user()->id)->where('nook_id',$nook->id)->first();

        if($review){
            $review->update([
                'ratting' => $input['rating'],
            ]);

            return [
                'message' => 'Review Updated Successfully',
                'review' =>  ReviewResource::make($review)
            ];
        }

        $review = Review::create([
            'ratting' => $input['rating'],
            'comment' => '',
            'user_id' => Auth::user()->id,
            'nook_id' => $nook->id,
        ]);

        return [
            'message' => 'Review Created Successfully',
            'review' =>  ReviewResource::make($review)
        ];

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
            'noOfBeds' => 'present',
            'address' => 'present',
            'area_unit' => 'present',
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
            'noOfBeds' => $input['noOfBeds'],
            'description' => $input['description'],
            'status' => Nook::$PENDING,
            'partner_id' => $user->id,
            'latLng_id' => isset($location)?$location->id:null,
            'address' => $input['address'],
            'area' => $input['independent_area'],
            'area_unit' => $input['area_unit'],
        ]);

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
    
    private function getMedia($id,$p){
        return Media::where('id',$id)->first();
    }
}
