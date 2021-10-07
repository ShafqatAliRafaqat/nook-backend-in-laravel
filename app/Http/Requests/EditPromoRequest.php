<?php

namespace App\Http\Requests;

use App\Promo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

class EditPromoRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){

        if(Auth::user()->can('promo-edit-all')){
            return true;
        }

        $id = $this->route('id');

        $promo = Promo::where('id',$id)->wherehas('restaurant',function ($qb){
           $qb->wherehas('managers',function ($q){
               $q->where('id',Auth::id());
           });
        })->first();

        if($promo && ($this->type == Promo::$DISCOUNT)){
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){

        $rules = CreatePromoRequest::$BASE_RULES;

        $rules['code'] = 'required';

        if($this->type != Promo::$DISCOUNT){
            $rules['points'] = 'required';
        }

        return $rules;
    }


    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
