<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware('auth:sanctum')->group(function () {
    
    // AdminController végpontjai
    Route::get('/admin/agents', [AdminController::class, 'getAgents']);
    Route::delete('/admin/agents/{id}', [AdminController::class, 'deleteAgent']);
    
});