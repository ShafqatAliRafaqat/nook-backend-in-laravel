<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model {

    protected $guarded = ['id'];

    public static $PENDING = 'pending';
    public static $IN_PROGRESS = 'in_progress';
    public static $APPROVED = 'approved';
    public static $REJECTED = 'rejected';
    public static $OFFBOARD = 'off-board';
    public static $CANCELLED = 'cancelled';

    public static $STATUS = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'off-board' => 'OffBoard',
        'cancelled' => 'Cancelled',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function receipts(){
        return $this->hasMany('App\Receipt', 'user_id', 'user_id');
    }

    public function nook(){
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }

    public function room(){
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }
}
