<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductOperation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_operation', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('quantite')->unsigned();
            $table->integer('prix')->unsigned()->nullable();
            
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_operation');
    }
}
