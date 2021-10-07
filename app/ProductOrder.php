<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model{

    protected $guarded = ['id'];

    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }

    public function order(){
        return $this->belongsTo('App\Order','order_id','id');
    }

    public function addons(){

        return $this->belongsToMany('App\ProductAddon',
            'product_order_addons',
            'product_order_id',
            'product_addon_id'
        )->withPivot(['options']);

    }

}
