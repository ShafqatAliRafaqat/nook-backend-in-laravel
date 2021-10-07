<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\SMSSendingHelper;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserResource;

class APINumberVerificationController extends Controller {


    public function send(){

        $user = Auth::user();

        $userDetails = $user->userDetails;

        if($userDetails->numberVerified){
            abort(400,__('messages.number.already.verified'));
        }

        $token = rand(10000, 99999);

        $userDetails->update([
           'number_code' => $token
        ]);

        $message = __('messages.number.verification.message',[
            'appName' => env('APP_NAME','Rabbit'),
            'token' => $token
        ]);

        $ssh = new SMSSendingHelper();

        if (!$ssh->send($user->number,$message)){
            abort(400,__('messages.number.verification.message.send.error'));
        }

        return response()->json([
            'message' => __('messages.number.verification.message.send.success'),
            'token' => $token,
        ]);

    }

    public function verify(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'code' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $userDetails = Auth::user()->userDetails;

        if($userDetails->number_code != $input['code']){
            abort(400,__('messages.number.verification.code.invalid'));
        }

        $userDetails->update([
            'numberVerified' => 1
        ]);
        $id = Auth::user()->id;
        $user = User::findOrFail($id);

        return response()->json([
            'message' => __('messages.number.verification.success'),
            'user' => UserResource::make($user),
        ], 200);

    }

}
