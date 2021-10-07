<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model {

    protected $guarded = ['id'];

    // when receipt is in_progress at the time of generating new receipt, mark old receipt as unpaid and
    // move all payable amount to next receipt
    public static $UNPAID = "unpaid";
    public static $DRAFT = "draft"; // every first state of receipt
    public static $PAID = "paid"; // when payment is accepted by admin or partner, mark receipt as paid
    public static $IN_PROGRESS = "in_progress"; // in_progress when receipt is issued and not paid

    protected $dates = ['due_date'];

    public static $STATUS = [
        'draft' => 'Draft',
        'unpaid' => 'Unpaid',
        'in_progress' => 'In Progress',
        'paid' => 'Paid',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function nook(){
        return $this->belongsTo('App\Nook', 'nook_id', 'id');
    }

    public function transaction(){
        return $this->hasOne('App\Transaction', 'receipt_id', 'id');
    }

}
