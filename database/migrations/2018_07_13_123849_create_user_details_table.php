<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {

        Schema::create('user_details', function (Blueprint $table) {

            $table->integer('user_id');

            $table->string('address')->nullable();
            $table->string('cnic')->nullable();
            $table->string('occupation')->nullable();
            $table->string('city')->nullable();

            $table->string('gender')->nullable();

            $table->string('imei')->nullable(); //

            $table->boolean('numberVerified')->default(0);
            $table->boolean('aggreedToTerms')->default(0);

            $table->integer('number_code')->default(0);
            $table->string('device_token')->nullable();

            $table->primary('user_id');

            $table->integer('profile_id')->default(0);

            $table->integer('room_id')->default(0);
            $table->integer('nook_id')->default(0);

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
        Schema::dropIfExists('user_details');
    }
}
