<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    protected $guarded = ['id'];

    public function products(){
        return $this->belongsToMany('App\Product','category_product','category_id','product_id');
    }

    public function restaurant(){
        return $this->belongsTo('App\Restaurant','rest_id','id');
    }


}
