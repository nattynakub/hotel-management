<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('app_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->dateTime('checkin_date');
            $table->string('durations', 10)->nullable();
            $table->string('total_guests', 10)->nullable();
            $table->string('total_kids', 10)->nullable();
            $table->string('total_rooms', 10)->nullable();
            $table->unsignedInteger('floor_id')->default(0);
            $table->unsignedInteger('room_id')->default(0);
            $table->string('payment_type', 5)->nullable();
            $table->decimal('total_price',10,2)->default(0);
            $table->timestamps();
            // $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_transactions');
    }
}
