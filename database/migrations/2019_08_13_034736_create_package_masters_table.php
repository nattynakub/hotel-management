<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('package_code', 50)->nullable();
            $table->string('package_name', 100)->nullable();
            $table->string('package_type', 10)->nullable();
            $table->string('package_benefit', 50)->nullable();
            $table->integer('package_discount_percentage')->default(0);
            $table->integer('package_discount')->default(0);
            $table->decimal('package_price',10,2)->default(0);
            $table->string('package_currency', 50)->nullable();
            $table->text('package_description')->nullable();
            $table->text('package_agreement')->nullable();
            $table->text('package_image')->nullable();
            $table->string('package_status', 1)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_masters');
    }
}
