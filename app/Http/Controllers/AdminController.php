<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
public function getAgents()
{
    return User::where('role', 'agent')
        ->with(['subscriptions'])
        ->get()
        ->map(function($agent) {
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'email' => $agent->email,
                'phone' => $agent->phone ?? 'Nincs megadva',
                'created_at' => $agent->created_at->format('Y.m.d'),
                'subscription_left' => $agent->subscription('default') ? $agent->subscription('default')->ends_at->diffForHumans() : 'Nincs aktív',
            ];
        });

}
    public function getAllBookings()
    {
    return \App\Models\Booking::with(['availability.calendar.user'])
        ->latest()
        ->get()
        ->map(function($booking) {
            return [
                'id' => $booking->id,
                'guest_name' => $booking->guest_name,
                'agent_name' => $booking->availability->calendar->user->name,
                'date' => $booking->availability->slot_time,
                'is_verified' => $booking->is_used, // QR kód érvényesítve
                'paid_deposit' => $booking->paid_deposit ?? false, // Feltételezve, hogy van ilyen meződ
            ];
        });
}
   /*   public function getAgents(Request $request){

     if ($request->user()->role !== 'admin')
    {
        return response()->json(['message'=> " Nincs jogosultságod ehhez a végponthoz"], 403);
    }

    $agents = User::where('role', 'agent')->orderBy('created_at', 'desc')->get();

    return response()->json($agents,200);

     } */


  public function deleteAgent(Request $request, $id)
    {
        
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Nincs jogosultságod!'], 403);
        }

        
        $agent = User::where('role', 'agent')->findOrFail($id);

    
        foreach ($agent->calendars as $calendar) {
            foreach ($calendar->availabilities as $availability) {
                
                Booking::where('availability_id', $availability->id)->delete();
                
                $availability->delete();
            }
            
            $calendar->delete();
        }

       
        $agent->tokens()->delete();

        
        $agent->delete();

        return response()->json([
            'message' => 'Ingatlanos és minden adata sikeresen törölve a rendszerből.'
        ], 200);
    }
     public function getDashboardData(Request $request)
    {
        $agents = User::where('role', 'agent')
            ->with(['calendars.availabilities'])
            ->get()
            ->map(function ($agent) {
                
                
                $isSubscribed = $agent->subscribed('default');
                $subscription = $agent->subscription('default');
                
                $status = 'Inaktív';
                $timeLeft = 'Nincs előfizetés';

                if ($isSubscribed) {
                    if ($subscription->onGracePeriod()) {
                        $status = 'Lemondva (Fut)';
                        $timeLeft = $subscription->ends_at->diffForHumans();
                    } else {
                        $status = 'Aktív (Fizetős)';
                        $timeLeft = 'Folyamatos';
                    }
                } elseif ($agent->onGenericTrial()) {
                    $status = 'Próbaidőszak';
                    $timeLeft = $agent->trialEndsAt()->diffForHumans();
                }

               
                $totalCalendars = $agent->calendars->count();
                $freeSlots = 0;
                $bookedSlots = 0;

                foreach ($agent->calendars as $calendar) {
                    foreach ($calendar->availabilities as $slot) {
                        if ($slot->is_booked) {
                            $bookedSlots++;
                        } else {
                            $freeSlots++;
                        }
                    }
                }

                return [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'created_at' => $agent->created_at->format('Y.m.d'),
                    'subscription_status' => $status,
                    'time_left' => $timeLeft,
                    'stats' => [
                        'calendars' => $totalCalendars,
                        'free_slots' => $freeSlots,
                        'booked_slots' => $bookedSlots,
                    ]
                ];
            });

        $platformStats = [
            'total_agents' => $agents->count(),
            'active_subscriptions' => $agents->whereIn('subscription_status', ['Aktív (Fizetős)', 'Próbaidőszak'])->count(),
            'total_bookings' => $agents->sum('stats.booked_slots')
        ];

        return response()->json([
            'platform_stats' => $platformStats,
            'agents' => $agents
        ], 200);
    }
}
