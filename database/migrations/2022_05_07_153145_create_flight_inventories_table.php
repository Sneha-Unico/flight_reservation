<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_inventories', function (Blueprint $table) {
            $table->bigIncrements('id');
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
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->foreign('booking_id')
                  ->references('id')->on('bookings')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->double('price', 8, 2)->default(3000);
            $table->enum('status', ['available', 'booked','customer_hold','system_hold']);
            $table->timestamp('status_updated_at', $precision = 0);
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
        Schema::dropIfExists('flight_inventories');
    }
}
