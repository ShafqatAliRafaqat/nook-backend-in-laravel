<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up() {

        Schema::create('transactions', function (Blueprint $table) {

            $table->increments('id');
            $table->string('status')->default("pending");

            $table->integer('amount');

            $table->text('details');

            $table->string('payment_method')->default("cash");

            $table->integer('receipt_id');
            $table->integer('nook_id');
            $table->integer('user_id');
            $table->integer('media_id')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transactions');
    }
}
