<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model{

    protected $guarded = ['id'];

    public function restaurants() {
        return $this->morphedByMany(
            'App\Restaurant',
            'taggable'
        );
    }

}
