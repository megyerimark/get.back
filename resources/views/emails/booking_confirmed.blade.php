<!DOCTYPE html>
<html>
<body>
    <h1>Szia {{ $booking->guest_name }}!</h1>
    <p>A foglalásodat rögzítettük az alábbi időpontra: <strong>{{ $booking->availability->slot_time }}</strong></p>
    <p>A QR-kódot a levél csatolmányában találod.</p>
    <p>Üdvözlettel,<br>A Getingo Csapata</p>
</body>
</html>