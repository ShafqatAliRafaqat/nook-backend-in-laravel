<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('nooks', function (Blueprint $table) {

            $table->increments('id');

            $table->string('type');
            $table->string('gender_type')->nullable();
            $table->string('space_type');

            $table->string('nookCode')->nullable();
            $table->string('status')->defaul("pending");
            $table->text('description')->nullable();
            $table->text('facilities')->nullable();
            $table->string('video_url')->nullable();
            $table->string('number');
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zipCode')->nullable();
            $table->string('address')->nullable();
            $table->integer('securityPercentage')->default(200);

            // family nook attributes
            $table->string('area')->nullable(); 
            $table->string('area_unit')->nullable(); 
            $table->text('inner_details')->nullable(); 
            $table->text('other')->nullable(); 
            $table->boolean('furnished')->default(1); 

            $table->integer('rent')->default(0);
            $table->integer('security')->default(0);
            $table->integer('agreementCharges')->default(0);
            $table->string('agreementTenure')->nullable();

            $table->integer('latLng_id')->nullable();
            $table->integer('partner_id');
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
        Schema::dropIfExists('nooks');
    }
}
