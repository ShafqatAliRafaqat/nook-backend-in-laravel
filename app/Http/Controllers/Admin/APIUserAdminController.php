<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\QB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserAdminResource;
use App\User;
use App\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;


class APIUserAdminController extends BaseController {

    protected $permissions = [
        'index'=>'user-list',
        'edit'=>'user-edit'
    ];

    public function index(Request $request){

        $input = $request->all();

        $qb = User::with('userDetails')
            ->orderBy('created_at','DESC');

        $qb = QB::whereLike($input,"name",$qb);

        $qb = QB::whereLike($input,"number",$qb);

        if (Auth::user()->can('user-list-all')) {

            $param = is_numeric($request->search)?"number":"name";

            $qb = QB::whereLike([
                $param => $request->search
            ],$param,$qb);

        }else{

            $qb = QB::where([
                'number' => ($request->search == '') ? "03":$request->search
            ],"number",$qb);
        }


        $qb = QB::hasWhere('userDetails',$qb,'isActive','=',$request->isActive);

        $qb = QB::hasWhere('userDetails',$qb,'numberVerified','=',$request->numberVerified);

        $users = $qb->paginate();

        // $users->appends(Input::except('page'));

        return UserAdminResource::collection($users);

    }
    public function allUsers(Request $request){

        $input = $request->all();
        $qb = User::with('userDetails')->orderBy('created_at','DESC');
        $qb = QB::whereLike($input,"name",$qb);
        $qb = QB::whereLike($input,"number",$qb);
        if (Auth::user()->can('user-list-all')) {

            $param = is_numeric($request->search)?"number":"name";

            $qb = QB::whereLike([
                $param => $request->search
            ],$param,$qb);

        }else{

            $qb = QB::where([
                'number' => ($request->search == '') ? "03":$request->search
            ],"number",$qb);
        }
        $qb = QB::hasWhere('userDetails',$qb,'isActive','=',$request->isActive);
        $qb = QB::hasWhere('userDetails',$qb,'numberVerified','=',$request->numberVerified);
        $users = $qb->get();
        return UserAdminResource::collection($users);

    }
    public function edit(Request $request,$id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required',
            'address' => 'present',
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = User::where('id',$id)
            ->with('userDetails')->first();

        if(!$user){
            abort(400,__('messages.user.not.found'));
        }

        $secured = [
            'name'      => $input['name'],
            'is_active' => $input['is_active']
        ];

        if($request->number != $user->number){
            $validator = Validator::make($input,['number' => 'required|max:11|regex:[03[0-9]{9}]|unique:users']);
            if($validator->fails()){
                abort(400,$validator->errors()->first());
            }
            $secured['number'] = $input['number'];
        }

        $user->update($secured);

        $user->userDetails->update([
            'address' => $input['address'],
        ]);

        return response()->json(['message' => __('messages.user.update.success')]);
    }
    public function createUser(Request $request){

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
        return UserAdminResource::make($user);
    }
}
