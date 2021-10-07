<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model {

    protected $guarded = ['id'];

    protected $dates = ['start','end'];

    public static $APPROVED = 'approved';
    public static $PENDING = 'pending';
    public static $IN_PROGRESS = 'in_progress'; 

    public static $STATUS = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'canceled' => 'Canceled',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function partner(){
        return $this->belongsTo('App\User', 'partner_id', 'id');
    }

    public function nook(){
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }
}
