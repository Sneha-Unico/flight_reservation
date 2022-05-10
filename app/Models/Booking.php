<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $guarded = [];

    public function bookFlight($input,$flightInventory)
    {
        $charges = ($input['status'] == "on_hold") ? Config::get('constants.flightOnHoldCharges') : $flightInventory->price;
        $booking = new Booking();
        $booking->uuid = (string) Str::uuid();
        $booking->flight_id = $flightInventory->flight_id;
        $booking->airliner_id = $flightInventory->airliner_id;
        $booking->airliner_seat_id = $input['airliner_seat_id'];
        $booking->customer_id = auth('api')->user()->id;
        $booking->amount = $charges;
        $booking->status = $input['status'];
        $booking->save();

        return $booking;
    }
}
