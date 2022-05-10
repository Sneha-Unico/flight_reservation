<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FlightInventory extends Model
{
    protected $guarded = [];

    public function updateInventoryStatus($input,$flightInventory,$booking=NULL)
    {
        $status = ($input['status'] == 'confirmed') ? 'booked' : 'customer_hold';
        FlightInventory::where('id',$flightInventory->id)->update([
            'status' => $status,
            'status_updated_at' => Carbon::now(),
            'booking_id' => $booking->id
        ]);
    }
}
