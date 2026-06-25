<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// --- PUBLIKUS ÚTVONALAK (Vevőknek, bárki elérheti) ---
Route::get('/public/calendars/{id}', [BookingController::class, 'getPublicCalendar']);
Route::post('/public/bookings', [BookingController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->group(function () {
    
    // AdminController végpontjai
    Route::get('/admin/agents', [AdminController::class, 'getAgents']);
    Route::delete('/admin/agents/{id}', [AdminController::class, 'deleteAgent']);
    
    //ingatlanosoké
    
    Route::get('/calendars', [CalendarController::class, 'index']);
    Route::post('/calendars', [CalendarController::class, 'store']);
    Route::post('/calendars/{id}/availabilities', [CalendarController::class, 'addAvailability']);
    Route::post('/bookings/verify', [BookingController::class, 'verifyQrCode']);
    
});