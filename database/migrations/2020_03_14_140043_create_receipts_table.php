<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {

            $table->increments('id');
            $table->string('status');

            $table->string('month');
            $table->integer('rent');
            $table->integer('arrears');
            $table->integer('e_units');
            $table->integer('e_unit_cost');
            $table->integer('fine');
            $table->integer('amount');
            $table->integer('latePaymentCharges');
            $table->integer('total_amount');
            $table->integer('received_amount');
            $table->integer('remaining_payable');

            $table->date('due_date');
            $table->integer('late_day_fine'); // charges/day after due date

            $table->text('extras');

            $table->integer('user_id');
            $table->integer('nook_id');
            $table->integer('room_id');

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
        Schema::dropIfExists('receipts');
    }
}
