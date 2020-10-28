<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magazin_from_id')->nullable();
            $table->unsignedBigInteger('magazin_to_id')->nullable();
            $table->string('reference')->nullable();
            $table->date('date');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('description')->nullable();

            $table->foreign('magazin_from_id')->references('id')->on('magazins')->onDelete('set null');
            $table->foreign('magazin_to_id')->references('id')->on('magazins')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('operations');
    }
}
