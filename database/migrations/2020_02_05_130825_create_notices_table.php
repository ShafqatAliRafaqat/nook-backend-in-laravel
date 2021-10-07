<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
            $table->text('details');
            $table->integer('month')->default(1); // 1-12
            $table->date('checkout');
            $table->integer('user_id');
            $table->integer('nook_id');
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
        Schema::dropIfExists('notices');
    }
}
