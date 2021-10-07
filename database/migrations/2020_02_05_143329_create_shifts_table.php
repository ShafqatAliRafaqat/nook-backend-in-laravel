<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');

            $table->text('details');
            $table->string('status')->default('pending');

            $table->string('room_type');
            $table->integer('price_per_bed');

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
        Schema::dropIfExists('shifts');
    }
}
