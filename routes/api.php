<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('upload', [EventsController::class, 'index']);
Route::post('events', [EventsController::class, 'getAllEvents']);
Route::get('nextWeekFlight', [EventsController::class, 'getNextWeekFlight']);
Route::get('nextWeekStandBy', [EventsController::class, 'getNextWeekStandBy']);
Route::post('getFlights', [EventsController::class, 'getFlightsFromLocation']);