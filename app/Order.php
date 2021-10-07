<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $guarded = ['id'];

    public static $PENDING = "pending";
    public static $ACCEPTED = "accepted";
    public static $READY_TO_PICKUP = "ready_to_pickup";
    public static $ON_WAY = "on_way";
    public static $delivered = "delivered";

    public static $STATUS = [
        'pending' => 'Pending',
        'accepted' => 'Accepted',
        'ready_to_pickup' => 'Ready To Pickup',
        'on_way' => 'On Way',
        'delivered' => 'Delivered'
    ];

    public static $STATUS_COLORS = [
        'pending' => 'dc3545',
        'accepted' => '17a2b8',
        'ready_to_pickup' => '17a2b8',
        'on_way' => '007bff',
        'delivered' => '28a745'
    ];


    public static $HOME_DELIVERY = "home";
    public static $TAKE_AWAY = "take_away";

    public static $DELIVERY_TYPES = [
        'home' => 'Home Delivery',
        'take_away' => 'Take Away'
    ];

    protected $dates = [
        'delivery_time',
        'delivered_at',
        'pickup_time'
    ];

    public function productOrders(){
        return $this->hasMany('App\ProductOrder','order_id','id');
    }

    public function restaurant(){
        return $this->belongsTo('App\Restaurant','rest_id','id');
    }

    public function user(){
        return $this->belongsTo('App\User','user_id','id');
    }

    public function deliveryBoy(){
        return $this->belongsTo('App\User','deliveryBoy_id','id');
    }

    public function addressLocation(){
        return $this->belongsTo('App\LatLng','address_latLng_id','id');
    }

    public function currentLocation(){
        return $this->belongsTo('App\LatLng','location_latLng_id','id');
    }

    public function transaction(){
        return $this->hasOne('App\Transaction','order_id','id');
    }

    public function review(){
        return $this->hasOne('App\Review','order_id','id');
    }

}
