<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        Schema::create('products', function (Blueprint $table) {

            $table->increments('id');

            $table->string('name');
            $table->text('description')->nullable();
            $table->float('price');
            $table->boolean('isVeg');
            $table->boolean('isDeal');
            $table->boolean('isFamous');
            $table->integer('order'); // display order
            $table->integer('prep_time');
            $table->integer('discount')->default(0);

            // relationship
            $table->integer('media_id');
            $table->integer('type_id');
            $table->integer('rest_id');

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
        Schema::dropIfExists('products');
    }
}
