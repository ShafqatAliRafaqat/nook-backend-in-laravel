<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {

        Schema::create('orders', function (Blueprint $table) {

            $table->increments('id');

            $table->string('delivery_type')->defaul("home");
            $table->string('status')->defaul("pending");

            $table->float('sub_total');
            $table->float('delivery_fee');

            $table->integer('points');
            $table->float('discount')->default(0);

            $table->float('total');

            $table->float('service_fee');

            $table->string('comment')->nullable();

            $table->integer('prep_time');
            $table->dateTime('pickup_time')->nullable();

            $table->dateTime('delivery_time');
            $table->dateTime('delivered_at')->nullable();

            $table->integer('transaction_id')->default(0);

            // user details
            $table->string('name');
            $table->string('number');
            $table->string('deliver_address');


            // relationship

            $table->integer('address_latLng_id');
            $table->integer('location_latLng_id');
            $table->integer('user_id');
            $table->integer('deliveryBoy_id')->nullable();
            $table->integer('rest_id');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('orders');
    }
}
