<?php
/**
 * Created by PhpStorm.
 * User: Azeem
 * Date: 8/13/2018
 * Time: 10:31 AM
 */

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Validator;

class BaseController extends Controller {

    protected $permissions = [];

    public function __construct() {
        foreach ($this->permissions as $m => $p){
            $this->middleware("permission:$p", ['only' => [$m]]);
        }
    }

    protected function validateOrAbort($input,$rules){
        $validator = Validator::make($input,$rules);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }
    }

}
