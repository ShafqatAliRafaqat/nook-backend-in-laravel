<?php

namespace App\Http\Requests;

use App\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;

class EditProductRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {

        if(Auth::user()->can('product-edit-all')){
            return true;
        }

        $id = $this->route('id');

        $product = Product::where('id',$id)->wherehas('restaurant',function ($qb){
            $qb->wherehas('managers',function ($q){
                $q->where('id',Auth::id());
            });
        })->first();

        if($product && CreateProductRequest::isRestaurantOwner($this)){
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

        $rules = CreateProductRequest::$BASE_RULES;

        $rules['order'] = 'required|numeric';

        return $rules;
    }

    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
