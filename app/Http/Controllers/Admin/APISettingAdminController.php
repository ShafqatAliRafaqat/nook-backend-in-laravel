<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APISettingAdminController extends BaseController {

    protected $permissions = [
        'index'=>'setting-list',
        'edit' =>'setting-edit'
    ];

    public function index(){
        $settings = Setting::pluck('value','key');
        return $settings;
    }

    public function edit(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'settings' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        foreach ($input['settings'] as $key => $value){
            Setting::where('key',$key)->update([
                    'value' => $value
            ]);
        }

        return response()->json(['message' => __("messages.setting.update.success")]);
    }

}
