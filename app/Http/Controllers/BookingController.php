<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Availability;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
   
    public function getPublicCalendar($id)
    {
       
        $calendar = Calendar::with(['availabilities' => function($query) {
            $query->where('is_booked', false)->orderBy('slot_time', 'asc');
        }])->findOrFail($id);

        return response()->json($calendar, 200);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'availability_id' => 'required|exists:availabilities,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:50',
        ]);

       
        $availability = Availability::findOrFail($validated['availability_id']);

       
        if ($availability->is_booked) {
            return response()->json(['message' => 'Ez az időpont sajnos már elkelt!'], 422);
        }

       
        $booking = Booking::create([
            'availability_id' => $availability->id,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
        ]);

        $availability->update(['is_booked' => true]);

        return response()->json([
            'message' => 'Sikeres foglalás! Az ingatlanos hamarosan keresni fog.',
            'booking' => $booking
        ], 201);
    }
}