<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {

    use HasRoles;
    use Notifiable;

    protected $guarded = [];

    protected $guard_name = 'api';

    public static $STATUS = [
        'pending' => 'Pending',
        'on_way' => 'On Way',
        'delivered' => 'Delivered'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function room(){
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

    public function cards(){
        return $this->hasMany('App\Card','user_id','id');
    }
    public function nooks(){
        return $this->belongsToMany('App\Nook','bookings','user_id','nook_id')
            ->withPivot(['status','rent','security','paidSecurity']);
    }
    public function rooms(){
        return $this->belongsToMany('App\Nook','bookings','user_id','room_id')
            ->withPivot(['status','rent','security','paidSecurity']);
    }
    public function partnernooks(){
        return $this->hasMany('App\Nook','partner_id','id');
    }
    public function reviews(){
        return $this->hasMany('App\Review','user_id','id');
    }

    public function complaints(){
        return $this->hasMany('App\Complaint', 'user_id', 'id');
    }
    public function shifts(){
        return $this->hasMany('App\Shift', 'user_id', 'id');
    }
    
    public function roomshifts(){
        return $this->hasMany('App\RoomShift', 'user_id', 'id');
    }

    public function receipts(){
        return $this->hasMany('App\Receipt', 'user_id', 'id');
    }

    public function transactions(){
        return $this->hasMany('App\Transaction', 'user_id', 'id');
    }

    public function visits(){
        return $this->hasMany('App\Visit', 'user_id', 'id');
    }

    public function userDetails(){
        return $this->hasOne('App\UserDetails','user_id','id');
    }
}
