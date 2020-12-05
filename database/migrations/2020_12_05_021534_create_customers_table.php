<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->nullable();
            $table->string('type', 100);
            $table->string('title', 100);
            $table->string('name', 250);
            $table->string('fisrt_name', 100);
            $table->string('last_name', 100);
            $table->string('address', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('telephone', 100)->nullable();
            // $table->string('mobile', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('company', 100)->nullable();
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
        Schema::dropIfExists('customers');
    }
}
