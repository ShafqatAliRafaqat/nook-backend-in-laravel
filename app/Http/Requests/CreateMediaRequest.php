<?php

namespace App\Http\Requests;

use App\Restaurant;
use ErrorException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

class CreateMediaRequest extends FormRequest {


    public static $BASE_RULES = [
        'caption' => 'present',
        'alt' => 'present',
        'nook_id' => 'required|numeric'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {

        if(Auth::user()->can('media-create-all')){
            return true;
        }

        $rest = Restaurant::where('id',$this->rest_id)
            ->whereHas('managers',function ($qb){
                $qb->where('id',Auth::id());
            })->first();

        if($rest){
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return array_merge(self::$BASE_RULES,[
            'image'=>'required'
        ]);
    }

    /**
     * @param Validator $validator
     * @throws ErrorException
     */
    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
