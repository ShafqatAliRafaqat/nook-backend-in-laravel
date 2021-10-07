<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model {
    
    protected $guarded = ['id'];

    public static $INTERNET = 'internet';
    public static $CLEANING = 'cleaning';
    public static $ENTERTAINMENT = 'entertainment';
    public static $SECURITY = 'security';
    public static $FOOD = 'food';
    public static $MAINTENANCE = 'maintenance';
    public static $DISCIPLINE = 'discipline';
    public static $STAFF_RELATED = 'staff_related';
    public static $PRIVACY = 'privacy';
    public static $OTHER = 'other';

    public static $TYPES = [
        'maintenance'=> 'Maintenance',
        'car_wash'=>'Car Wash',
        'delivery'=>'Delivery',
        'security'=> 'Security',
        'charity_stuff'=> 'Charity Stuff',
        'staff_related'=> 'Staff Related',
        'privacy'=> 'Privacy',
        'internet'=> 'Internet',
        'food'=> 'Food',
        'cleaning'=> 'Cleaning',
        'entertainment'=> 'Entertainment',
        'discipline'=> 'Discipline', 
        'other'=> 'Other Support'
    ];

    public static $PENDING = "pending";
    public static $IN_PROGRESS = "in_progress";
    public static $APPROVED = "approved";
    public static $REJECTED = "rejected";

    public static $STATUS = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function partner()
    {
        return $this->belongsTo('App\User', 'to_user_id', 'id');
    }

    public function nook()
    {
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }
    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }
    
    public function media(){
        return $this->belongsTo('App\Media','media_id','id');
    }
}
