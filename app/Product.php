<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model{

    protected $guarded = ['id'];

    public function reviews(){
        return $this->hasMany('App\Review','product_id','id');
    }

    public function addons(){
        return $this->hasMany('App\ProductAddon','product_id','id');
    }

    public function type(){
        return $this->belongsTo('App\ProductType','type_id','id');
    }

    public function image(){
        return $this->belongsTo('App\Media','media_id','id');
    }

    public function restaurant(){
        return $this->belongsTo('App\Restaurant','rest_id','id');
    }

    public function categories(){
        return $this->belongsToMany('App\Category','category_product','product_id','category_id');
    }
}
