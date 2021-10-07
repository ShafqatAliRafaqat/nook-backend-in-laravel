<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {

        Schema::create('media', function (Blueprint $table) {

            $table->increments('id');

            $table->string('name');
            $table->string('caption')->nullable();
            $table->string('alt')->nullable();

            $table->string('small')->nullable();
            $table->string('medium')->nullable();
            $table->string('path');

            // relationships
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
        Schema::dropIfExists('media');
    }
}
