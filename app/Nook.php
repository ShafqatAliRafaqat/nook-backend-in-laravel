<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nook extends Model{

    protected $guarded = [];

    public static $SPACE_TYPES = [
        'shared' => 'Shared',
        'independent' => 'Independent',
        'service' => 'Service',
    ];

    public static $PENDING = "pending";
    public static $IN_PROGRESS = "inProgress";
    public static $APPROVED = "approved";
    public static $REJECTED = "rejected";

    public static $STATUS = [
        'pending' => 'Pending',
        'inProgress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public static $HOUSE = "house";
    public static $FLAT = "flat";
    public static $INDEPENDENT_ROOM = "independentRoom";
    public static $HOSTEL_BUILDING = "hostelBuilding";
    public static $OUTHOUSE = "outHouse";
    public static $OTHER = "other";

    public static $types = [
        'house' => 'House',
        'flat' => 'Flat',
        'upper_portion' => 'Upper Portion',
        'lower_portion' => 'Lower Portion',
        'farm_house' => 'Farm House',
        'pent_house' => 'Pent House',
        'independentRoom' => 'Independent Room',
        'hostelBuilding' => 'Hostel Building',
        'outHouse' => 'Out House',
        'other' => 'Other',
    ];

    public static $MALE = "male";
    public static $FEMALE = "female";
    public static $BOTH = "both";

    public static $nookTypes = [
        'male' => 'Male',
        'female' => 'Female',
        'both' => 'Both',
    ];

    public function location(){
        return $this->belongsTo('App\LatLng','latLng_id','id');
    }

    public function complaints(){
        return $this->hasMany('App\Complaint', 'nook_id', 'id');
    }

    public function notices(){
        return $this->hasMany('App\Notice', 'nook_id', 'id');
    }

    public function shifts(){
        return $this->hasMany('App\Shift', 'nook_id', 'id');
    }
    public function roomshifts(){
        return $this->hasMany('App\RoomShift', 'nook_id', 'id');
    }
    
    public function bookings(){
        return $this->hasMany('App\Bookings', 'nook_id', 'id');
    }

    public function receipts(){
        return $this->hasMany('App\Receipt', 'nook_id', 'id');
    }

    public function transactions(){
        return $this->hasMany('App\Transaction', 'nook_id', 'id');
    }

    public function visits(){
        return $this->hasMany('App\Visit', 'nook_id', 'id');
    }

    public function partner(){
        return $this->belongsTo('App\User','partner_id','id');
    }

    public function medias(){
        return $this->hasMany('App\Media','nook_id','id');
    }

    public function rooms(){
        return $this->hasMany('App\Room', 'nook_id', 'id');
    }

    public function users(){
        return $this->belongsToMany('App\User','bookings','nook_id','user_id');
    }

    public function reviews(){
        return $this->hasMany('App\Review','nook_id','id');
    }

}