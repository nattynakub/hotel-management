<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->text('message')->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('pointable_id')->default(0);
            $table->enum('pointable_type',['SYSTEM','ADDITION'])->default('SYSTEM');
            $table->decimal('amount',10,2)->default(0);
            $table->decimal('current',10,2)->default(0);
            $table->dateTime('expire_date')->nullable();
            $table->string('flags',1)->default('+');
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['pointable_id']);
            $table->index(['pointable_type']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_transactions');
    }
}
