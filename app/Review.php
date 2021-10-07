<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model{

    protected $guarded = ['id'];

    public function nook(){
        return $this->belongsTo('App\Nook','nook_id','id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

}
