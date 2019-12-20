<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageMemberDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_member_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_member_id')->default(0);
            $table->unsignedInteger('coupon_id')->default(0);
            $table->unsignedInteger('slot_id')->default(0);
            $table->enum('package_coupon',['FREENIGHT','ADDITIONAL'])->default('FREENIGHT');
            $table->boolean('transfer')->default(0);
            $table->string('passport_id', 24)->nullable();
            $table->string('nametitle', 10)->nullable();
            $table->string('firstname', 100)->nullable();
            $table->string('lastname', 100)->nullable();
            $table->string('email', 50)->nullable();
            $table->enum('gender',['M','F','O'])->nullable();
            $table->integer('kids')->default(0);
            $table->decimal('additional_price',10,2)->default(0);

            $table->integer('status')->default(0)->comment('{0=pending,1=available,2=cancel}');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['package_member_id']);
            $table->index(['package_coupon']);
            $table->index(['slot_id']);
            $table->foreign('coupon_id')->references('id')->on('coupon_masters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_member_details');
    }
}
