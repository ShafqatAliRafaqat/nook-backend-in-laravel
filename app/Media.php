<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model {

    protected $guarded = ['id'];

    public function nook(){
        return $this->belongsTo('App\Nook','nook_id','id');
    }

}
