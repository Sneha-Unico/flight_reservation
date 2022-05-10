<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from_city_id');
            $table->unsignedBigInteger('to_city_id');
            $table->date('departure_date');
            $table->date('arrival_date');
            $table->time('departure_time', $precision = 0);
            $table->time('arrival_time', $precision = 0);
            $table->timestamps();
            $table->foreign('from_city_id')->references('id')->on('cities')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('to_city_id')->references('id')->on('cities')->onDelete('restrict')->onUpdate('restrict');
            $table->index(['from_city_id', 'to_city_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
