<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'booking_token' => $user->booking_token,
            'is_subscribed' => $user->subscribed('default'), 
            'subscription_ends_at' => $user->subscription('default')?->ends_at,
        ]);
    }

   
    public function dashboard(Request $request)
    {
        
        return response()->json([
            'bookings' => $request->user()->bookings,
            'calendars' => $request->user()->calendars
        ]);
    }
}
