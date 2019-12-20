<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('partner_id')->default(0);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('agreement')->nullable();
            $table->enum('privileges_type',['FREE','POINT','PERCENTAGE','FIXED'])->default('FREE');
            $table->decimal('price',10,2)->default(0);
            $table->string('code', 50)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['partner_id']);
            $table->index(['privileges_type']);
            $table->index(['code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privileges');
    }
}
