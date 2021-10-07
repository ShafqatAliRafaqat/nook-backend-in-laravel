<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Media;
use App\Services\UserService;
use App\User;
use App\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;

class APIUserController extends Controller {

    public function details(){
        return UserResource::make(Auth::user());
    }

    public function update(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required',
            'number' => 'required',
            'gender' => 'required',
            'address' => 'required',
            'city' => 'required',
            'occupation' => 'required',
            'aggreedToTerms' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user_id = Auth::id();

        User::where('id',$user_id)->update([
            'name' => $input['name'],
            'number' => $input['number'],
        ]);

        $profile_id = 0;

        if($request->profile){
            $img = Image::make($request->profile);
            $result = FileHelper::getAndCreatePath($img->filename,'profiles');
            $extension = substr($img->mime,strpos($img->mime,'/')+1);
            $result['name'] = $result['name'].str_random(15).'.'.$extension;

            $path = $this->saveImage($img,'default',1,$result);
            $medium = $this->saveImage($img,'medium',0.75,$result);
            $small = $this->saveImage($img,'small',0.40,$result);

            $media = Media::create([
                'name' => $result['name'],
                'path' => $path,
                'small' => $small,
                'medium' => $medium,
            ]);

            $profile_id = $media->id;
        }


        UserDetails::where('user_id',$user_id)->update([
            'gender' => $input['gender'],
            'city' => $input['city'],
            'occupation' => $input['occupation'],
            'aggreedToTerms' => $input['aggreedToTerms'],
            'address' => $input['address'],
            'profile_id' => $profile_id,
        ]);

        $user = User::findOrFail($user_id);

        return response()->json([
            'message' => __('messages.user.update.success'),
            'user' => UserResource::make($user),
        ], 200);
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

    public function changePassword(Request $request){

        $input = $request->all();
        $input['user'] = Auth::user();
        $uService = new UserService();
        $response =  $uService->changePassword($input);

        if(!$response['success']) {
            abort(400,$response['message']);
        }

        return response()->json(['message' => $response['message']]);
    }

}
