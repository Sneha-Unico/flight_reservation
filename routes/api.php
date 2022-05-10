<?php

use Illuminate\Http\Request;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/generate-uuid',function(){
    return (string) Str::uuid();
});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//auth routes
Route::post('user-register', 'AuthController@register');
Route::post('user-login', 'AuthController@login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/all-cities', 'ApiController@cities')->name('cities');
    Route::post('/search', 'ApiController@searchFlights')->name('search-flights');
    Route::post('/flight/{uuid}/show', 'ApiController@getFlightDetails')->name('get-flight-details');
    Route::post('/flight/{uuid}/seat-status', 'ApiController@flightSeats')->name('flight-seats');
    Route::post('/flight/seat-availability', 'ApiController@checkFlightSeatAvailability')->name('check-flight-seat-availability');
    Route::post('/book-now', 'ApiController@bookNow')->name('book-now');
});