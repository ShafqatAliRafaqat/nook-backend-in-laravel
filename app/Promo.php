<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model{

    protected $guarded = ['id'];

    protected $dates = ['expiry'];

    public static $DISCOUNT = 'discount';
    public static $SING_UP = 'sign_up';
    public static $REFER_FRIEND = 'refer_friend';

    public static $TYPES = [
        'discount'=>'Discount',
        'sign_up'=>'SignUp - Get Credit',
        'refer_friend'=>'Refer friend - Get Credit',
    ];

    public function restaurant(){
        return $this->belongsTo('App\Restaurant','rest_id','id');
    }

    public function scopeDiscountPromo($qb){
        return $qb->where('type',self::$DISCOUNT);
    }

    public function scopeSignUpPromo($qb){
        return $qb->where('type',self::$SING_UP);
    }

    public function scopeReferFriendPromo($qb){
        return $qb->where('type',self::$REFER_FRIEND);
    }

    public function scopeExcludeExpired($qb){
        return $qb->whereDate('expiry','>',Carbon::now()->toDateString());
    }

}
