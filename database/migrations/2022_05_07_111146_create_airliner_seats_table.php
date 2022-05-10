<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirlinerSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airliner_seats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('airliner_id');
            $table->foreign('airliner_id')
                  ->references('id')->on('airliners')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');
            $table->string('seat_number');
            $table->timestamps();
            $table->index(['airliner_id', 'seat_number']);
            $table->unique(['airliner_id', 'seat_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airliner_seats');
    }
}
