<?php

namespace App\Http\Traits;

use Illuminate\Http\Response;
use App\Models\FlightInventory;

trait CheckSeatAvailability
{

    public function checkSeatAvailability($flight, $airliner_seat_id)
    {
        $flightInventory = FlightInventory::select('flight_inventories.*','airliner_seats.seat_number')
                            ->where('flight_id',$flight->id)
                            ->where('flight_inventories.airliner_id',$flight->airliner_id)
                            ->where('flight_inventories.airliner_seat_id',$airliner_seat_id)
                            ->join("airliner_seats","airliner_seats.id","=","flight_inventories.airliner_seat_id")                         
                            ->first(); 
        return $flightInventory;
    }
}
