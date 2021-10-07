<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {

    protected $guarded = ['id'];

    public static $CASH = "cash";
    public static $CARD = "card";

    public static $PENDING = "pending";
    public static $IN_PROGRESS = "in_progress";
    public static $APPROVED = "approved";
    public static $REJECTED = "rejected";

    public static $PAYMENT_METHODS = [
        'cash' => 'Cash',
        'card' => 'Card'
    ];


    public static $STATUS = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];


    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function nook(){
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }

    public function receipt(){
        return $this->belongsTo('App\Receipt', 'receipt_id', 'id');
    }

    public function media(){
        return $this->belongsTo('App\Media','media_id','id');
    }

}
