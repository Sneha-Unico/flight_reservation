<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlinersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airliners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('airline_id');
            $table->foreign('airline_id')
                  ->references('id')->on('airlines')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->string('model_number',100)->unique();
            $table->string('registration_number',100)->unique();
            $table->integer('capacity');
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
        Schema::dropIfExists('airliners');
    }
}
