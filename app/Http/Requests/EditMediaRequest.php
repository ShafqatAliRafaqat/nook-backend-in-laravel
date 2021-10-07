<?php

namespace App\Http\Requests;

use App\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class EditMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {

        if(Auth::user()->can('media-edit-all')){
            return true;
        }

        $id = $this->route('id');

        $media = Media::where('id',$id)->wherehas('restaurant',function ($qb){
            $qb->wherehas('managers',function ($q){
                $q->where('id',Auth::id());
            });
        })->first();

        if($media){
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
        return array_merge(CreateMediaRequest::$BASE_RULES,[
            'name' => 'required',
        ]);
    }

    protected function failedValidation(Validator $validator){
        abort(400,$validator->errors()->first());
    }
}
