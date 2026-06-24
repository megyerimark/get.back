<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Availability;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
   
    public function index(Request $request)
    {
      
        $calendars = Calendar::where('user_id', $request->user()->id)
            ->with(['availabilities' => function($query) {
                $query->orderBy('slot_time', 'asc'); 
            }])
            ->get();

        return response()->json($calendars, 200);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'external_url' => 'nullable|url' 
        ], [
            'title' => 'Kérlek add meg az ingatlan címét',
            'external_url' => 'ingatlan.com-os linked'
        ]);

        $calendar = Calendar::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'external_url' => $validated['external_url'] ?? null,
        ]);

        return response()->json(['message' => 'Naptár sikeresen létrehozva!', 'calendar' => $calendar], 201);
    }

    
    public function addAvailability(Request $request, $calendarId)
    {
        $validated = $request->validate([
            'slot_time' => 'required|date'
        ]);

      
        $calendar = Calendar::where('user_id', $request->user()->id)->findOrFail($calendarId);

        $availability = Availability::create([
            'calendar_id' => $calendar->id,
            'slot_time' => $validated['slot_time'],
            'is_booked' => false 
        ]);

        return response()->json(['message' => 'Időpont sikeresen rögzítve!', 'availability' => $availability], 201);
    }
}