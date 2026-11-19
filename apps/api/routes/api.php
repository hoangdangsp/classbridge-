<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\SessionController;

// API v1 — legacy, frozen except for security fixes.
// Traffic is monitored; v1 retires after 30 consecutive days at zero.

Route::prefix('v1')->group(function () {
    Route::get('/sessions', [SessionController::class, 'indexLegacy']);
});

// API v2 — strangler migration target. Every endpoint here must pass the
// contract suite against its v1 counterpart before merge.

Route::prefix('v2')->group(function () {
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::post('/sessions/{session}/attendance', [SessionController::class, 'markAttendance']);
});
