<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_periods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('transfer_to')->default(0);
            $table->unsignedInteger('package_id')->default(0);
            $table->string('package_coupon', 50)->nullable();
            $table->string('package_additional_coupon', 50)->nullable();
            $table->dateTime('package_period_start_time');
            $table->dateTime('package_period_end_time');
            $table->string('status', 1)->nullable()->comment('{0=pending,1=available,2=cancel}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['transfer_to']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('package_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_periods');
    }
}
