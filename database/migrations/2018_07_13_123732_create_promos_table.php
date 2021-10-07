<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('promos', function (Blueprint $table) {

            $table->increments('id');

            $table->string('title');
            $table->text('details');

            $table->string('type');

            $table->string('code')->unique();

            $table->integer('discount');
            $table->integer('maxAmount');

            $table->dateTime('expiry');

            $table->integer('points')->default(0);;

            // relationships
            $table->integer('rest_id')->default(0);

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
        Schema::dropIfExists('promos');
    }
}
