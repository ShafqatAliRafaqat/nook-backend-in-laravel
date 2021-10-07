<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class CreateOrEditRestaurantRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        return [
            'name' => 'required',
            'description' => 'present',
            'isVeg' => 'required',
            'delivery_time' => 'required|numeric',
            'min_delivery' => 'required|numeric',
            'delivery_fee' => 'required|numeric',
            'free_delivery_price' => 'required|numeric',
            'about' => 'present',
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'media_id' => 'required|numeric',
            'tags' => 'required',
            'timeSlots' => 'required',
            'managers' => 'required',
        ];
    }

    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
