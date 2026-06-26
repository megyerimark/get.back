<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SubscriptionController;

// --- PUBLIKUS ÚTVONALAK ---
Route::get('/public/calendars/{id}', [BookingController::class, 'getPublicCalendar']);
Route::post('/public/bookings', [BookingController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// --- VÉDETT ÚTVONALAK (Csak bejelentkezve) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Admin
    Route::get('/admin/agents', [AdminController::class, 'getAgents']);
    Route::delete('/admin/agents/{id}', [AdminController::class, 'deleteAgent']);
    

    
    Route::middleware('verified')->group(function () { 
        
        
        Route::post('/subscription/checkout', [SubscriptionController::class, 'createCheckoutSession']);

        // Csak aktív SaaS előfizetéssel
        Route::middleware('subscribed')->group(function () { 
             Route::get('/my-premium-dashboard', [AgentController::class, 'premiumData']);
             Route::get('/agent/profile', [AgentController::class, 'profile']);
             Route::get('/agent/dashboard', [AgentController::class, 'dashboard']);
             Route::get('/calendars', [CalendarController::class, 'index']);
             Route::post('/calendars', [CalendarController::class, 'store']);
             Route::post('/calendars/{id}/availabilities', [CalendarController::class, 'addAvailability']);
             Route::post('/bookings/verify', [BookingController::class, 'verifyQrCode']);
        }); 
    });
});