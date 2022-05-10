<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('uuid'); 
            $table->unsignedBigInteger('flight_id');
            $table->foreign('flight_id')
                  ->references('id')->on('flights')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('airliner_id');
            $table->foreign('airliner_id')
                  ->references('id')->on('airliners')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('airliner_seat_id');
            $table->foreign('airliner_seat_id')
                  ->references('id')->on('airliner_seats')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')
                  ->references('id')->on('customers')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->double('amount', 8, 2);
            $table->enum('status', ['pending', 'confirmed','on_hold','cancelled']);
            $table->index(['flight_id', 'airliner_id','airliner_seat_id','status'],'flight_airliner_airliner_seat_id_status_index');
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
        Schema::dropIfExists('bookings');
    }
}
