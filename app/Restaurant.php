<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model {

    protected $guarded = ['id'];

    public static $PENDING = "pending";
    public static $APPROVED = "approved";

    public static $STATUS = [
        'pending' => 'Pending Approval',
        'approved' => 'Approved'
    ];


    public function reviews(){
        return $this->hasMany('App\Review','rest_id','id');
    }

    public function promos(){
        return $this->hasMany('App\Promo','rest_id','id');
    }

    public function medias(){
        return $this->hasMany('App\Media','rest_id','id');
    }

    public function timeSlots(){
        return $this->hasMany('App\TimeSlot','rest_id','id');
    }

    public function categories(){
        return $this->hasMany('App\Category','rest_id','id');
    }

    public function products(){
        return $this->hasMany('App\Product','rest_id','id');
    }

    public function orders(){
        return $this->hasMany('App\Order','rest_id','id');
    }

    public function managers(){
        return $this->belongsToMany('App\User','user_restaurant','rest_id','user_id');
    }

    public function tags(){
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function location(){
        return $this->belongsTo('App\LatLng','latLng_id','id');
    }

    public function image(){
        return $this->belongsTo('App\Media','media_id','id');
    }

}
