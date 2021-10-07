<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Exceptions\UnauthorizedException;

class DashboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize() {

        $permission = "restaurant-create";

        if(!Auth::user()->can($permission)){
            throw UnauthorizedException::forPermissions([$permission]);
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            //
        ];
    }

}
