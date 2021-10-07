<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('restaurants', function (Blueprint $table) {

            $table->increments('id');

            $table->string('name');
            $table->text('description')->nullable();

            $table->string('status')->defaul("pending");

            $table->boolean('isVeg');
            $table->integer('delivery_time');
            $table->integer('min_delivery');

            $table->float('delivery_fee');
            $table->float('free_delivery_price');

            $table->string('address')->nullable();
            $table->text('about')->nullable();

            // relationship
            $table->integer('latLng_id');
            $table->integer('media_id');

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
        Schema::dropIfExists('restaurants');
    }
}
