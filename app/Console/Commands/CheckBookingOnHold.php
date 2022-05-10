<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\FlightInventory;
use App\Models\Booking;

class CheckBookingOnHold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:checkBookingOnHold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if booking is on hold for more than 3 hours than cancel it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $getBookingIdOnHold = FlightInventory::where('status','customer_hold')->where('status_updated_at','<',Carbon::now()->subHours(3))->pluck('booking_id');
        Booking::whereIn('id',$getBookingIdOnHold)->update(['status'=>'cancelled']);
        FlightInventory::whereIn('booking_id',$getBookingIdOnHold)->update(['status'=>'available']);
    }
}
