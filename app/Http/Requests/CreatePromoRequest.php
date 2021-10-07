<?php

namespace App\Http\Requests;

use App\Promo;
use App\Restaurant;
use Auth;
use ErrorException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreatePromoRequest extends FormRequest {


    public static $BASE_RULES = [
        'title' => 'required',
        'details' => 'required',
        'type' => 'required',
        'code' => 'required|unique:promos',
        'discount' => 'required|numeric',
        'maxAmount' => 'required|numeric',
        'expiry' => 'required|numeric',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {

        if(Auth::user()->can('promo-create-all')){
            return true;
        }

        $rest = Restaurant::where('id',$this->rest_id)
            ->whereHas('managers',function ($qb){
                $qb->where('id',Auth::id());
            })->first();

        if($rest && ($this->type == Promo::$DISCOUNT)){
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

        $rules = self::$BASE_RULES;

        $rules['rest_id'] = 'required';

        if($this->type != Promo::$DISCOUNT){
            $rules['points'] = 'required';
            $rules['rest_id'] = 'present';
        }

        return $rules;
    }


    /**
     * @param Validator $validator
     * @throws ErrorException
     */
    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }


}
