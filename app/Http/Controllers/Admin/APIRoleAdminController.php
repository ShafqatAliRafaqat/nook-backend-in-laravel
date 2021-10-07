<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\RoleResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class APIRoleAdminController extends BaseController {


    protected $permissions = [
        'index'=>'role-list',
        'userRoles' => 'user-role-list',
        'create'=>'role-create',
        'updateRoles' => 'user-role-update'
    ];

    public function index(){
        return RoleResource::collection(Role::all());
    }

    public function userRoles($id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => __('messages.user.not.found')]);
        }

        return RoleResource::collection($user->roles);
    }

    public function create(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        Role::create([
            'name'=>$input['name']
        ]);

        return response()->json(['message' => __('messages.role.create.success')]);

    }

    public function updateRoles(Request $request,$id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'roles' => 'present'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $user = User::find($id);

        if(!$user){
            abort(400,__('messages.user.not.found'));
        }

        $user->syncRoles($input['roles']);

        return response()->json(['message' => __('messages.user.roles.updated')]);
    }


}
