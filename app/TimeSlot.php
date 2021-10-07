<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model{

    protected $guarded = ['id'];

    public function restaurant(){
        return $this->belongsTo('App\Restaurant','rest_id','id');
    }

}
