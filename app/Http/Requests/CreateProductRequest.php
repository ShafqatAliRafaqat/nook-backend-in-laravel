<?php

namespace App\Http\Requests;

use App\Restaurant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

class CreateProductRequest extends FormRequest {

    public static $BASE_RULES = [
        'name' => 'required',
        'description' => 'required',
        'price' => 'required|numeric',
        'isVeg' => 'required',
        'isDeal' => 'required',
        'isFamous' => 'required',
        'discount' => 'required|numeric',
        'prep_time' => 'required|numeric',
        'media_id' => 'required|numeric',
        'type_id' =>'required|numeric',
        'rest_id' => 'required|numeric',
        'categories' => 'required',
    ];


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {

        if(Auth::user()->can('product-create-all')){
            return true;
        }

        return self::isRestaurantOwner($this);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules() {
        return self::$BASE_RULES;
    }


    public static function isRestaurantOwner($t){
        $rest = Restaurant::where('id',$t->rest_id)
            ->whereHas('managers',function ($qb){
                $qb->where('id',Auth::id());
            })->first();

        return ($rest != null);
    }

    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
