<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('adresse_1')->nullable();
            $table->string('adresse_2')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('phone')->nullable();
            $table->string('faxe')->nullable();
            $table->string('site')->nullable();
            $table->string('email')->nullable();

            $table->unsignedBigInteger('devise_id')->nullable();
            $table->foreign('devise_id')->references('id')->on('devises')->onDelete('set null');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametres');
    }
}
