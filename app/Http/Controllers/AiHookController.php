<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Events\IncidentApproved;

class AiHookController extends Controller
{
    public function handle(Request $r)
    {
        if ($r->input('type') !== 'sustained_fire_signal') {
            return response()->json(['ok'=>true]);
        }

        $incident = Incident::create([
            'name' => 'AI Kamera İhbarı',
            'source' => 'ai_stream',
            'status' => 'pending_approval', // isterseniz auto_approved
            'confidence' => $r->input('fire_prob'),
            // konum yoksa boş; kameranın bilinen koordinatını Station üzerinden doldurabilirsiniz
        ]);

        // burayı politikanıza göre değiştirin
        // otomatik onay derseniz:
        // $incident->status = 'approved'; $incident->save();
        // IncidentApproved::dispatch($incident);

        return response()->json(['ok'=>true, 'incident_id'=>$incident->id]);
    }
}
