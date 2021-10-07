<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model{

    protected $primaryKey = 'user_id';

    protected $guarded = ['id'];

    public static $MALE = "male";
    public static $FEMALE = "female";

    public static $genders = [
        'male' => 'Male',
        'female' => 'Female',
    ];

    public function profile(){
        return $this->belongsTo('App\Media','profile_id','id');
    }
    public function user(){
        return $this->hasOne('App\User','id','user_id');
    }
    public function room(){
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

}
