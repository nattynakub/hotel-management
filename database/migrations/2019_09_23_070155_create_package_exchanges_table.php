<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_exchanges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('exchange_code', 50)->nullable();
            $table->unsignedInteger('owner_id')->default(0);
            $table->unsignedInteger('owner_pmd_id')->default(0);
            $table->unsignedInteger('receiver_id')->default(0);
            $table->unsignedInteger('receiver_pmd_id')->default(0);
            $table->enum('type',['POST','REQUEST'])->default('POST');
            $table->dateTime('post_date')->nullable();
            $table->dateTime('exchange_date')->nullable();
            $table->dateTime('confirm_date')->nullable();
            $table->integer('exchange_status')->default(0)->comment('{0=Wait,1=Approve,2=Reject,3=Request,9=Expired}');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['exchange_code']);
            $table->index(['owner_id']);
            $table->index(['receiver_id']);
            $table->index(['owner_pmd_id']);
            $table->index(['receiver_pmd_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_exchanges');
    }
}
