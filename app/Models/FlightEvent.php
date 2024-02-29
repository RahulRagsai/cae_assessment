<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;

class FlightEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'check_in',
        'check_out',
        'activity',
        'activity_type',
        'from',
        'std',
        'to',
        'sta',
        'week_id',
        'created_at',
        'updated_at'
    ];
    
    public function week()
    {
        return $this->belongsTo(Week::class, 'week_id');
    }

    public static function getEvents($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])->get();
    }

    public static function nextWeekFlights($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])->where('activity_type', 'FLT')->get(); 
    }

    public static function nextWeekStandBy($startDate, $endDate)
    {
        return self::whereBetween('date', [$startDate, $endDate])->where('activity_type', 'SBY')->get(); 
    }

    public static function getFlights($location)
    {
        return self::where('from', $location)->get();
    }
}
