<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Booking;
use App\Models\Calendar;
use Illuminate\Http\Request;
use App\Mail\BookingMailable;

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
    // 1. Validáció
    $validated = $request->validate([
        'availability_id' => 'required|exists:availabilities,id',
        'guest_name' => 'required|string|max:255',
        'guest_phone' => 'required|string|max:50',
        'guest_email' => 'required|email',
    ]);

    // 2. Ellenőrzés
    $availability = Availability::findOrFail($validated['availability_id']);
    if ($availability->is_booked) {
        return response()->json(['message' => 'Ez az időpont sajnos már elkelt!'], 422);
    }

    // 3. Foglalás létrehozása - MOST JÖN LÉTRE A $booking
    $booking = Booking::create([
        'availability_id' => $availability->id,
        'guest_name' => $validated['guest_name'],
        'guest_phone' => $validated['guest_phone'],
        'guest_email' => $validated['guest_email'],
        'is_used' => false, // Alapértelmezett érték
    ]);

    $availability->update(['is_booked' => true]);

    // 4. CSAK EZUTÁN jöhet az email és QR küldés

    //Ha élesben használod, a getingo.hu címet kell beírnod.
    try {
        $qrData = "https://getingo.hu/verify-booking?id=" . $booking->id; 
        $qrImage = (string) QrCode::format('png')->size(250)->generate($qrData);

        // A Mailable osztályt használjuk, mert ez a legbiztonságosabb
        Mail::to($booking->guest_email)->send(new BookingMailable($booking, $qrImage));
        
    } catch (\Exception $e) {
        \Log::error("Email küldési hiba: " . $e->getMessage());
        // Itt nem returnölünk 500-ast, mert a foglalás már sikeres volt!
    }

    return response()->json([
        'message' => 'Sikeres foglalás!',
        'booking' => $booking
    ], 201);
}
public function verifyQrCode(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->is_used) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ezt a QR-kódot már felhasználták!'
            ], 400);
        }

        $booking->update(['is_used' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sikeres azonosítás!',
            'guest_name' => $booking->guest_name
        ], 200);
    }
} // Ez a lezáró zárójel
   
   /*  public function store(Request $request)
    {
        $validated = $request->validate([
            'availability_id' => 'required|exists:availabilities,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:50',
            'guest_email' => 'required|email',
        ], [
            'guest_name' => "nem maradhat üresen",
            'guest_phone' =>" kötelező mező",
            'guest_email'=> " kötelező"


        ]);
        $qrData = "Foglalás: " . $booking->id . " | Név: " . $booking->guest_name;
        $qrCode = QrCode::format('png')->size(200)->generate($qrData);
        Mail::send('emails.booking_confirmed', ['booking' => $booking, 'qrCode' => base64_encode($qrCode)], function($message) use ($booking) {
        $message->to($booking->guest_email)->subject('Foglalásod megerősítése');
});
       
        $availability = Availability::findOrFail($validated['availability_id']);

       
        if ($availability->is_booked) {
            return response()->json(['message' => 'Ez az időpont sajnos már elkelt!'], 422);
        }

       
        $booking = Booking::create([
            'availability_id' => $availability->id,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
        ]);

        $availability->update(['is_booked' => true]);

        return response()->json([
            'message' => 'Sikeres foglalás! Az ingatlanos hamarosan keresni fog.',
            'booking' => $booking
        ], 201);
    } */
/*    public function store(Request $request)
    {
        $validated = $request->validate([
            'availability_id' => 'required|exists:availabilities,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:50',
            'guest_email' => 'required|email',
        ], [
            'guest_name' => "nem maradhat üresen",
            'guest_phone' => " kötelező mező",
            'guest_email' => " kötelező"
        ]);
        $qrData = $booking->id;

        // 1. Ellenőrizzük, hogy elérhető-e az időpont
        $availability = Availability::findOrFail($validated['availability_id']);

        if ($availability->is_booked) {
            return response()->json(['message' => 'Ez az időpont sajnos már elkelt!'], 422);
        }

        // 2. Létrehozzuk a foglalást
        $booking = Booking::create([
            'availability_id' => $availability->id,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
        ]);

        // 3. Frissítjük az időpontot foglaltra
        $availability->update(['is_booked' => true]);
        // 4. Generáljuk a QR-t
        $qrData = "Foglalás: " . $booking->id . " | Név: " . $booking->guest_name;
        $qrImage = (string) QrCode::format('png')->size(250)->generate($qrData);

        // 5. Email küldése (Itt adjuk át a qrImage-t a tömbben!)
        Mail::send('emails.booking_confirmed', [
            'booking' => $booking,
            'qrImage' => $qrImage // <-- EZ HIÁNYZOTT!
        ], function($message) use ($booking) {
            $message->to($booking->guest_email)
                    ->subject('Foglalásod megerősítése - Getingo');
        });

        return response()->json([
            'message' => 'Sikeres foglalás! Az ingatlanos hamarosan keresni fog.',
            'booking' => $booking
        ], 201);

    }
    public function verifyQrCode(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->is_used) {
           
            return response()->json([
                'status' => 'error',
                'message' => 'Ezt a QR-kódot már felhasználták!'
            ], 400);
        }

        $booking->update(['is_used' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sikeres azonosítás!',
            'guest_name' => $booking->guest_name
        ], 200);
    } */


