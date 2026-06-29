<!DOCTYPE html>
<html>
<body>
    <h1>Szia {{ $booking->guest_name }}!</h1>
    <p>A foglalásodat rögzítettük az alábbi időpontra: <strong>{{ $booking->availability->slot_time }}</strong></p>
    <img src="{{$message->embedData($qrImage,'qr-kod.png')}}" alt="">
    <p>A QR-kódot a levél csatolmányában találod.</p>
    <p>Ha a QR-kód nem olvasható, kérlek diktáld be az ingatlanosnak ezt a 6 jegyű kódot:</p>
<h2 style="letter-spacing: 5px; color: #d4af37;">{{ $booking->verification_code }}</h2>
    <p>Üdvözlettel,<br>A Getingo Csapata</p>
</body>
</html>