<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model {
    protected $guarded = ['id'];


    public function nook(){
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }

    public function roomuser(){
        return $this->hasMany('App\UserDetails','room_id','id');
    }

    public function users(){
        return $this->belongsToMany('App\User','bookings','room_id','user_id')->where('bookings.status','approved');
    }

    public function shifts(){
        return $this->hasMany('App\Shift', 'room_id', 'id');
    }
    public function roomshifts(){
        return $this->hasMany('App\RoomShift', 'room_id', 'id');
    }
    public function bookings(){
        return $this->hasMany('App\Bookings', 'room_id', 'id');
    }
    public function complaints(){
        return $this->hasMany('App\Complaint', 'room_id', 'id');
    }
}
