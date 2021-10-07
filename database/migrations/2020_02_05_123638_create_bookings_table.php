<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->integer('rent');
            $table->integer('installments')->default(0);
            $table->integer('security')->default(0);
            $table->integer('paidSecurity')->default(0);
            $table->integer('refunedSecurity')->default(0);
            $table->integer('user_id');
            $table->integer('nook_id');
            $table->integer('room_id')->default(0);
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
        Schema::dropIfExists('bookings');
    }
}
