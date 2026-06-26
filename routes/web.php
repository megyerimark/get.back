<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Érvénytelen vagy lejárt link!'], 403);
    }

    // Ha már meg van erősítve
    if ($user->hasVerifiedEmail()) {
        return redirect('http://localhost:4200/login?verified=already');
    }

    // Megerősítés rögzítése az adatbázisban
    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    // Sikeres megerősítés után átdobjuk az Angular bejelentkezési oldalára!
    return redirect('http://localhost:4200/login?verified=success');

})->middleware(['signed'])->name('verification.verify');