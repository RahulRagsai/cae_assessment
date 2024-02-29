<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use App\Models\FlightEvent;
use Exception;
use Carbon\Carbon;

class EventsController extends Controller
{
    use FileUploadTrait;
    public function index(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:html,htm|max:2048',
        ]);

        $file = $request->file('file');
        $result = $this->uploadFile($file);
        $parsedContent = $this->parseFile($result['content']);
        
        return response()->json($this->store($parsedContent));
    } 

    public function store($data)
    {
        try {
            FlightEvent::insert($data);
            return [
                'success' => true,
                'message' => 'Events imported successfully.'
            ];
        } catch(Exception $e) {
            return [
                'success' => false, 
                'message' => "Something went wrong."
            ];
        }
    }

    public function getAllEvents(Request $request)
    {
        if($request->has('start_date') && $request->has('end_date')) {
            try {
                $events = FlightEvent::getEvents($request->start_date, $request->end_date);
                $data = [];
                if($events->count() > 0) {
                    foreach($events as $event) {
                        $data[] = $this->asApiResponse($event);
                    }
                    return [
                        'success' => true,
                        'data' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'No events found.',
                        'data' => $data
                    ];
                }
            } catch(Exception $e) {
                return [
                    'success' => false, 
                    'message' => "Something went wrong."
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Please provide start_date and end_date'
            ];
        }
    }

    public function getNextWeekFlight()
    {
        try {
            $nextWeek = $this->nextWeek('2022-01-14');
            $flights = FlightEvent::nextWeekFlights($nextWeek['startsAt'], $nextWeek['endsAt']);
            $data = [];
            if($flights->count() > 0) {
                foreach($flights as $flight) {
                    $data[] = $this->asApiResponse($flight);
                }
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No flights found.',
                    'data' => $data
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false, 
                'message' => "Something went wrong."
            ];
        }   
    }

    public function getNextWeekStandBy()
    {
        try{
            $nextWeek = $this->nextWeek('2022-01-14');
            $standBy = FlightEvent::nextWeekStandBy($nextWeek['startsAt'], $nextWeek['endsAt']);
            $data = [];
            if($standBy->count() > 0) {
                foreach($standBy as $sb) {
                    $data[] = $this->asApiResponse($sb);
                }
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No stand by flights found.',
                    'data' => $standBy
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false, 
                'message' => "Something went wrong."
            ];
        }
    }

    public function getFlightsFromLocation(Request $request)
    {
        if($request->has('location')) {
            $flights = FlightEvent::getFlights($request->location);
            $data = [];
            if($flights->count() > 0) {
                foreach($flights as $flight) {
                    $data[] = $this->asApiResponse($flight);
                }
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No flights found.',
                    'data' => $data
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Please provide location.'
            ];
        }
        
    }

    public function asApiResponse($data)
    {
        return [
            "id" => $data->id,
            "date" => $data->date,
            "check_in" => $data->check_in,
            "check_out" => $data->check_out,
            "activity" => $data->activity,
            "activity_type" => $data->activity_type,
            "from" => $data->from,
            "std" => $data->std,
            "to" => $data->to,
            "sta" => $data->sta,
            "week" => isset($data->week->name) ? $data->week->name : ""
        ];   
    }

    public function nextWeek($date)
    {
        $currentDate = Carbon::createFromFormat('Y-m-d', $date);
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $nextWeekStartsAt = $currentDate->copy()->startOfWeek()->addWeek();
        $nextWeekEndsAt = $currentDate->copy()->endOfWeek()->addWeek();
        
        return [
            'startsAt' => $nextWeekStartsAt->format('Y-m-d'),
            'endsAt' => $nextWeekEndsAt->format('Y-m-d'),
        ];
    }
}
