<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\SocialAccount;
use App\User;
use App\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class APILoginRegisterController extends Controller {

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request) {

        $validator = Validator::make($request->all(),[
            'number' => 'required',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $credentials = request(['number', 'password']);

        $user = User::where('number',$credentials['number'])->first();

        if(!$user){
            $user = User::where('email',$credentials['number'])->first();
        }

        if(!$user){
            abort(401,__('messages.login.error.message'));
        }


        if (!Hash::check($credentials['password'], $user->password)) {
            abort(401,__('messages.login.error.message'));
        }
        
        if($user->is_active == 0){

            abort(401,__('Account is not active or blocked. Please contact the customer service.'));
        }
        
        $token = auth()->login($user);
        $user->token = $token;
        $payload = UserResource::make($user)->toArray($request);

        return response()->json(array_merge(['message'=>__('messages.login.success.message')],$payload));
    }

    public function socialLogin(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'provider_user_id' => 'required',
            'provider' => 'required',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = $this->getUserFromSocialAccount($request);

        $token = auth()->login($user);

        $user = Auth::user();
        $user->token = $token;
        $payload = UserResource::make($user)->toArray($request);

        return response()->json(array_merge(['message'=>__('messages.login.success.message')],$payload));
    }

    private function getUserFromSocialAccount(Request $request){

        $input = $request->all();

        $account = SocialAccount::whereProvider($input['provider'])
            ->whereProviderUserId($input['provider_user_id'])
            ->first();

        if($account){
            return $account->user;
        }


        $user = User::create([
            'name' => $input['name'],
        ]);

        $user->assignRole(env('PARTNER_ROLE_NAME','Partner'));
        
        SocialAccount::create([
            'provider_user_id' => $input['provider_user_id'],
            'provider' => $input['provider'],
            'user_id' => $user->id,
        ]);

        UserDetails::create([
            'user_id' => $user->id,
        ]);

        return $user;
    }


    public function register(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required|max:255',
            'number' => 'required|max:11|regex:[03[0-9]{9}]|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = User::create([
            'name' => $input['name'],
            'number' => $input['number'],
            'password' => bcrypt($input['password'])
        ]);

        $user->assignRole(env('PARTNER_ROLE_NAME','Partner'));

        UserDetails::create([
            'user_id' => $user->id,
        ]);


        $token = auth()->login($user);

        $user = Auth::user();
        $user->token = $token;
        $payload = UserResource::make($user)->toArray($request);

        return response()->json(array_merge([
            'message'=>__('messages.register.success.message')
        ],$payload));
    }
}
