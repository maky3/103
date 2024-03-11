<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TicketController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/requests', [TicketController::class, 'index']);
    Route::put('/requests/{id}', [TicketController::class, 'update']);
    Route::post('/requests', [TicketController::class, 'store']);
});
