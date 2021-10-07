<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    
     public function up() {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('capacity');
            $table->integer('noOfBeds');
            $table->float('price_per_bed');
            $table->string('room_number')->nullable();
            $table->integer('nook_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
