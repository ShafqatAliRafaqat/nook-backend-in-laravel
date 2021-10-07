<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_addons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('isMultiSelectable');
            $table->text('options'); // json(name,price)

            // relationship

            $table->integer('product_id');

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
        Schema::dropIfExists('product_addons');
    }
}
