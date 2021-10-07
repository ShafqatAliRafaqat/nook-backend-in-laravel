<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model {

    protected $guarded = [];

    public static function getValue($key){
        $search = Setting::where('key',$key)->first();
        if($search){
            return $search->value;
        }
        return '';
    }
}