<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvailableSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_slots', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slot_code', 50)->nullable();
            $table->string('slot_name', 255)->nullable();
            $table->dateTime('slot_start_date')->nullable();
            $table->dateTime('slot_end_date')->nullable();
            $table->string('slot_year', 5)->nullable();
            $table->string('slot_week', 5)->nullable();
            $table->enum('slot_peak',['NORMAL','PEAK','HIGH'])->default('NORMAL');
            $table->integer('slot_room')->default(0);
            $table->integer('slot_room_remain')->default(0);
            $table->unsignedInteger('room_type_id')->default(0);
            $table->integer('slot_status')->default(0)->comment('{0=wait,1=available,2=cancel,3=pending}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['slot_code']);
            $table->index(['room_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('available_slots');
    }
}
