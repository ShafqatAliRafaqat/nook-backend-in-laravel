<?php

namespace App\Http\Requests;

use App\Order;
use App\Promo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderAdminRestaurateurRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {

        if(Auth::user()->can('order-admin-all')){
            return true;
        }

        $id = $this->route('id');

        $order = Order::where('id',$id)->wherehas('restaurant',function ($qb){
            $qb->wherehas('managers',function ($q){
                $q->where('id',Auth::id());
            });
        })->first();

        if($order){
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
