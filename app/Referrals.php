<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Referrals extends Model{

    protected $guarded = ['id'];

    public function referee(){
        return $this->belongsTo('App\User','referee_id','id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

}
