<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// YENİ EKLENECEK KOD
// Bu kanal, sadece belirtilen rollerdeki kullanıcıların dinlemesine izin verir.
Broadcast::channel('alarms', function ($user) {
    return in_array($user->role, ['admin', 'itfaiye', 'bakanlik']);
});
// routes/api.php

// Bu rota, yapay zeka/drone'dan durum güncellemelerini alacak
Route::post('/v1/drone/status-update/{incident}', function (Request $request, App\Models\Incident $incident) {
    $validated = $request->validate([
        'status' => 'required|string',
        'message' => 'nullable|string',
    ]);

    $incident->drone_status = $validated['status'];
    $incident->save();
    
    // TODO: Bu durumu anlık olarak panele yansıtmak için bir Event yayınla
    // event(new DroneStatusUpdated($incident));

    return response()->json(['message' => 'Drone status updated successfully.']);
})->middleware('auth:sanctum');
Broadcast::channel('alarms', function ($user) {
    return in_array($user->role, ['admin', 'itfaiye', 'bakanlik']);
});

// YENİ EKLENECEK KOD
// Her bir olayın özel kanalını sadece yetkili rollerin dinlemesine izin ver.
Broadcast::channel('incident.{id}', function ($user, $id) {
    return in_array($user->role, ['admin', 'itfaiye', 'bakanlik']);
});
