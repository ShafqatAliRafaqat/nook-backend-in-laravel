<?php

namespace App\Services;

use App\Helpers\EncryptionHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService {

    public function changePassword($input){

        $validator = Validator::make($input,[
            'old_password' => 'required',
            'password' => 'required|string|min:6',
            'user' => 'required'
        ]);

        if($validator->fails()){
            return ['success' => false,'message' => $validator->errors()->first()];
        }

        $user = $input['user'];

        if(!Hash::check($input['old_password'],$user->password)){
            return ['success' => false,'message' => __('messages.user.password.does.not.match')];
        }

        $oldHash = md5($input['old_password']);
        $oldKey = env('SERVER_ENCRYPTION_KEY')."_$oldHash";

        $newHash = md5($input['password']);
        $newKey = env('SERVER_ENCRYPTION_KEY')."_$newHash";

        // get user's cards

        $cards = $user->cards()->get();

        foreach ($cards as $card){
            $payload = EncryptionHelper::decrypt($oldKey,$card->payload);
            $payload = EncryptionHelper::encrypt($newKey,$payload);
            $card->update([
                'payload' => $payload
            ]);
        }

        $user->update([
            'password' => Hash::make($input['password'])
        ]);

        return ['success' => true,'message' => __('messages.user.password.update.success')];
    }
}