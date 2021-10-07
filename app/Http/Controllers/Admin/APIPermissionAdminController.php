<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ArrayHelper;
use App\Http\Controllers\BaseController;
use App\Http\Resources\PermissionResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class APIPermissionAdminController extends BaseController {

    protected $permissions = [
        'index'=>'permission-list',
        'rolePermissions'=>'role-permission-list',
        'userPermissions'=>'user-permission-list',
        'create'=>'permission-create',
        'updatePermissions'=>'role-permission-update',
    ];

    public function index(){
        $permissions = Permission::orderBy('name')->get();
        return PermissionResource::collection($permissions);
    }

    public function rolePermissions($id){

        $role = Role::find($id);

        if(!$role){
            abort(400,__('messages.role.not.found'));
        }

        $permissions = $role->permissions()->orderBy('name')->get();

        return PermissionResource::collection($permissions);
    }

    public function userPermissions($id){

        $user = User::find($id);

        if(!$user){
            return response()->json(['message' => __('messages.user.not.found')]);
        }

        $permissions = [];

        foreach ($user->roles as $role){
            $prs = $role->permissions()->pluck('name')->toArray();
            $permissions = ArrayHelper::array_merge($permissions,$prs);
        }

        return $permissions;
    }

    public function create(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        Permission::create([
            'name'=>$input['name']
        ]);

        return response()->json(['message' => __('messages.permission.create.success')]);
    }

    public function updatePermissions(Request $request,$id){

        $input = $request->all();

        $validator = Validator::make($input,[
            'permissions' => 'present'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        $role = Role::find($id);

        if(!$role){
            abort(400,__('messages.role.not.found'));
        }

        $role->syncPermissions($input['permissions']);

        return response()->json(['message' => __('messages.role.permissions.updated')]);
    }

}
