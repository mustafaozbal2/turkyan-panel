<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\IncidentReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Yapay zeka tespitlerini bu kapıdan alacağız
Route::post('/v1/incidents/report-from-ai', [IncidentReportController::class, 'store'])
     ->middleware('auth:sanctum');

// Drone'dan durum güncellemelerini bu kapıdan alacağız
Route::post('/v1/drone/status-update/{incident}', function (Request $request, App\Models\Incident $incident) {
    $validated = $request->validate([
        'status' => 'required|string',
        'message' => 'nullable|string',
    ]);

    $incident->drone_status = $validated['status'];
    $incident->save();
    
    // YENİ: Drone durumu güncellendiğinde event'i yayınla!
    event(new \App\Events\DroneStatusUpdated($incident));

    return response()->json(['message' => 'Drone status updated successfully.']);
})->middleware('auth:sanctum');
