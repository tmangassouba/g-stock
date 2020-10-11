<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->string('code')->nullable();
            $table->string('ref_fabricant')->nullable();
            $table->string('description')->nullable();
            $table->integer('stock_min')->unsigned()->nullable()->default(0);
            $table->integer('stock_max')->unsigned()->nullable();
            $table->integer('prix')->unsigned()->nullable();
            // unité
            $table->integer('quantite')->unsigned()->nullable();
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
        Schema::dropIfExists('products');
    }
}
