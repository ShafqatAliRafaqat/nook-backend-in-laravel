<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIContactController extends Controller {

    public function save(Request $request){

        $input = $request->all();

        $validator = Validator::make($input,[
            'name' => 'required',
            'number' => 'required|max:11|regex:[03[0-9]{9}]',
            'message' => 'required'
        ]);

        if($validator->fails()){
            abort(400,$validator->errors()->first());
        }

        Contact::create([
            'name' => $input['name'],
            'number' => $input['number'],
            'message' => $input['message'],
        ]);

        return response()->json(['message' => __('messages.add.contact.success')]);
    }
}
