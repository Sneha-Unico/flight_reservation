<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Validator;
use App\Http\Resources\FlightResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Models\City;
use App\Models\Schedule;
use App\Models\Flight;
use App\Models\FlightInventory;
use App\Models\Booking;
use App\Models\Airliner;
use App\Http\Traits\CheckSeatAvailability;

class ApiController extends Controller
{
    use CheckSeatAvailability;

    public function __construct(){
        $this->bookings = new Booking();
        $this->flightInventory = new FlightInventory();
    }

    /**
     * @OA\Post(
     *      path="/api/all-cities",
     *      operationId="getCityList",
     *      tags={"Cities"},
     *      security={{"bearer":{}}},
     *      summary="Get list of cities",
     *      description="Returns list of cities",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function cities()
    {
        $cities = City::all();
        return $this->respondWithSuccess($cities);
    }

    /**
     * @OA\Post(
     *      path="/api/search",
     *      operationId="getFlights",
     *      tags={"Search Flights"},
     *      security={{"bearer":{}}},
     *      summary="Get list of flights",
     *      description="Returns list of flights",
     *      @OA\Parameter(
     *          name="from_city_id",
     *          in="query",
     *          required=true,
     *          description="From City Id",
     *           @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="to_city_id",
     *          in="query",
     *          required=true,
     *          description="To City Id",
     *           @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="date",
     *          in="query",
     *          required=true,
     *          description="Date of Departure",
     *           @OA\Schema(
     *              type="string",
     *              format="date"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function searchFlights(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_city_id' => 'required|exists:cities,id',
            'to_city_id' => 'required|exists:cities,id',
            'date' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails())
        {
            return $this->respondWithValidationError($validator->errors()->getMessages());
        }
        $input = $request->all();
        $schedules = Schedule::where('from_city_id',$input['from_city_id'])
                ->where('to_city_id',$input['to_city_id'])
                ->where('departure_date',$input['date']);
                if($input['date'] == today()->format('Y-m-d')){
                    $schedules->where('departure_time','>',Carbon::now()->format('H:i:s'));
                }
        $schedules = $schedules->pluck('id');
        $flights = Flight::select('flights.uuid','flights.flight_number','airlines.name')->whereIn('schedule_id',$schedules)
                        ->join('airliners','airliners.id','=','flights.airliner_id')
                        ->join('airlines','airlines.id','=','airliners.airline_id')->get();
        return $this->respondWithSuccess($flights);
    }

    /**
     * @OA\Post(
     *      path="/api/flight/{uuid}/show",
     *      operationId="getFlightDetails",
     *      tags={"Flights"},
     *      security={{"bearer":{}}},
     *      summary="Get details of a flight",
     *      description="Returns details of a flight",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="UUID",
     *           @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function getFlightDetails(Request $request,$uuid)
    {
        $flights = Flight::select('flights.uuid','flights.flight_number','airlines.name as airline','from_city.name as from_city','to_city.name as to_city','schedules.departure_date','schedules.departure_time','schedules.arrival_date','schedules.arrival_time','flight_statuses.name as flight_status')
                        ->where('uuid',$uuid)
                        ->join('airliners','airliners.id','=','flights.airliner_id')
                        ->join('airlines','airlines.id','=','airliners.airline_id')
                        ->join('schedules','schedules.id','=','flights.schedule_id')
                        ->join('cities as from_city','from_city.id','=','schedules.from_city_id')
                        ->join('cities as to_city','to_city.id','=','schedules.to_city_id')
                        ->join('flight_statuses','flight_statuses.id','=','flights.flight_status_id')
                        ->first();
        if($flights)
            return $this->respondWithSuccess($flights);
        else
            return $this->respondWithError('Invalid Flight');
    }

    /**
     * @OA\Post(
     *      path="/api/flight/{uuid}/seat-status",
     *      operationId="getFlightSeatStatus",
     *      tags={"Flights"},
     *      security={{"bearer":{}}},
     *      summary="Get seat details of a flight",
     *      description="Returns seat details of a flight",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="path",
     *          required=true,
     *          description="UUID",
     *           @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function flightSeats(Request $request,$uuid)
    {
        $flight = Flight::where('uuid',$uuid)->first();
        if(!$flight)
        {
            return $this->respondWithError('Invalid Flight');
        }
        $flightInventory = FlightInventory::select('flight_inventories.airliner_seat_id','airliner_seats.seat_number','flight_inventories.status')
                            ->where('flight_id',$flight->id)
                            ->where('flight_inventories.airliner_id',$flight->airliner_id)
                            ->join("airliner_seats","airliner_seats.id","=","flight_inventories.airliner_seat_id");                          
        $flightInventory = $flightInventory->get();
        return $this->respondWithSuccess($flightInventory);
    }

    /**
     * @OA\Post(
     *      path="/api/flight/seat-availability",
     *      operationId="getFlightSeatAvailability",
     *      tags={"Flights"},
     *      security={{"bearer":{}}},
     *      summary="Get a seat detail of a flight",
     *      description="Returns a seat detail of a flight",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="query",
     *          required=true,
     *          description="UUID",
     *           @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="airliner_seat_id",
     *          in="query",
     *          required=true,
     *          description="Airliner Seat Id",
     *           @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function checkFlightSeatAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|uuid|exists:flights,uuid',
            'airliner_seat_id' => 'required|exists:airliner_seats,id'
        ]);
        if ($validator->fails())
        {
            return $this->respondWithValidationError($validator->errors()->getMessages());
        }
        $input = $request->all();
        $flight = Flight::where('uuid',$input['uuid'])->first();
        
        //Check if 85-90% seats are booked. If not, simulate.
        $flightBookedPercent = 0;
        $airlinerCapacity = Airliner::where('id',$flight->airliner_id)->value('capacity');
        $flightBooked = FlightInventory::where('flight_id',$flight->id)->where('status','!=','available')->count();
        $simulateSeatInPercent = Config::get('constants.simulateSeatInPercent');
        if($flightBooked > 0)
        {
            $flightBookedPercent = ($airlinerCapacity * 100) / $flightBooked;
        }
        if($flightBookedPercent < $simulateSeatInPercent)
        {
            $remainingBookingPercent = $simulateSeatInPercent - $flightBookedPercent;
            $rowCountToSimulateUpdate = round(($remainingBookingPercent / 100) * $airlinerCapacity);
            FlightInventory::where('flight_id',$flight->id)->where('status','available')->inRandomOrder()->limit($rowCountToSimulateUpdate)->update([
                'status' => 'system_hold',
                'status_updated_at' => Carbon::now()
            ]);
        }
        $flightInventory = $this->checkSeatAvailability($flight,$input['airliner_seat_id']);
        return $this->respondWithSuccess($flightInventory);

    }

        /**
     * @OA\Post(
     *      path="/api/book-now",
     *      operationId="getFlightSeatAvailability",
     *      tags={"Book Now"},
     *      security={{"bearer":{}}},
     *      summary="Get a seat detail of a flight",
     *      description="Returns a seat detail of a flight",
     *      @OA\Parameter(
     *          name="uuid",
     *          in="query",
     *          required=true,
     *          description="UUID",
     *           @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="airliner_seat_id",
     *          in="query",
     *          required=true,
     *          description="Airliner Seat Id",
     *           @OA\Schema(
     *              type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          in="query",
     *          required=true,
     *          description="Booking Status [on_hold, confirmed]",
     *           @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="Not found"
     *   ),
     *  )
     */
    public function bookNow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|uuid|exists:flights,uuid',
            'airliner_seat_id' => 'required|exists:airliner_seats,id',
            'status' => 'required|in:on_hold,confirmed'
        ]);
        if ($validator->fails())
        {
            return $this->respondWithValidationError($validator->errors()->getMessages());
        } 
        $input = $request->all();
        //Check Seat Availability
        $flight = Flight::where('uuid',$input['uuid'])->first();
        $flightInventory = $this->checkSeatAvailability($flight,$input['airliner_seat_id']);
        if($flightInventory->status == 'available')
        {
            //Book Flight
            $booking = $this->bookings->bookFlight($input,$flightInventory);
            $booking = Booking::where('uuid',$booking->uuid)->first();
            //Update Inventory Status
            $this->flightInventory->updateInventoryStatus($input,$flightInventory,$booking);
            return $this->respondWithSuccess(['booking_uuid' => $booking->uuid]);
        }
        else if ($flightInventory->status == 'customer_hold')
        {
            $booking = Booking::where('id',$flightInventory->booking_id)->first();
            if($booking->customer_id == auth('api')->user()->id)
            {
                $booking->status = $input['status'];
                $booking->amount = $flightInventory->price;
                $booking->save();

                FlightInventory::where('id',$flightInventory->id)->update([
                    'status' => ($input['status'] == 'confirmed') ? 'booked' : $flightInventory->status,
                    'status_updated_at' => Carbon::now()
                ]);
                return $this->respondWithSuccess(['booking_uuid' => $booking->uuid]);
            }
        }
        else
        {
            return $this->respondWithError('Selected Seat is not available');
        }

    }

}
