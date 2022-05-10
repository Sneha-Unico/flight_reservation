<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid'); 
            $table->string('flight_number',50);
            $table->unsignedBigInteger('airliner_id');
            $table->foreign('airliner_id')
                  ->references('id')->on('airliners')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')
                  ->references('id')->on('schedules')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('flight_status_id');
            $table->foreign('flight_status_id')
                  ->references('id')->on('flight_statuses')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->timestamps();
            $table->index(['flight_number', 'airliner_id','schedule_id','flight_status_id'],'flights_no_airliner_id_schedule_id_flight_status_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
