<?php

namespace App\Http\Controllers;

use App\Helpers\SMSSendingHelper;
use App\Setting;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class APIPasswordResetController extends Controller {


    public function send(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'number' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = User::with('userDetails')
            ->where('number',$input['number'])
            ->first();

        if(!$user){
            abort(400,__('messages.user.not.found'));
        }

        $token = rand(100000, 999999);

        $message = __('messages.password.reset.message',[
            'appName' => env('APP_NAME','Rabbit'),
            'token' => $token
        ]);

        $ssh = new SMSSendingHelper();

        if (!$ssh->send($user->number,$message)){
            abort(400,__('messages.password.reset.send.code.error'));
        }

        DB::table('password_resets')->updateOrInsert([
            'number' => $input['number']
        ],[
            'number' => $input['number'],
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        return response()->json([
            'message' => __('messages.password.reset.send.code.success'),
            'token' => ''
        ]);

    }

    public function change(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'token' => 'required',
            'number' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $code_expiry = Setting::getValue('password_reset_code_expiry');

        $validDateTime = Carbon::now()->subMinutes($code_expiry)->toDateTimeString();

        $qb = DB::table('password_resets')->where([
            ['token',$input['token']],['number',$input['number']]
        ])->where("created_at",">",$validDateTime);

        if(!$qb->exists()){
            abort(400,__('messages.password.reset.code.not.valid'));
        }

        User::where('number',$input['number'])->update([
            'password' => Hash::make($input['password'])
        ]);

        $qb->delete();

        return response()->json(['message' =>__('messages.password.reset.success')]);

    }




}
