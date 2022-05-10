<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\FlightInventory;
class PriceHikeBeforeDeparture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:piceHikeBeforeDeparture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hike prices before departure';

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
        $hoursBeforeDepartureForPriceHike = Config::get('constants.hoursBeforeDepartureForPriceHike');
        $departureTimeForHike = []; 
        for($i=$hoursBeforeDepartureForPriceHike; $i>=1; $i--)
        {
            array_push($departureTimeForHike,Carbon::now()->subHours($i)->format('H:i'));
        }
        $departureTimeForHike = implode("','",$departureTimeForHike);
        \DB::connection()->enableQueryLog();
        $flights = Schedule::join('flights','flights.schedule_id','=','schedules.id')->whereDate('departure_date',today())->whereRaw("TIME_FORMAT(`departure_time`,'%H:%i') IN ('".$departureTimeForHike."')")->pluck('flights.id');
        $queries = \DB::getQueryLog();
        FlightInventory::whereIn('flight_id',$flights)->where('status','!=','booked')->update([
            'price' => \DB::raw('price + (0.5*price)')
        ]);
    }
}
