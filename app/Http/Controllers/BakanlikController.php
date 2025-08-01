<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\FireStation;
use App\Models\WaterSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BakanlikController extends Controller
{
    public function index()
    {
        $allIncidents = Incident::latest()->get();
        $fireStations = FireStation::all(); // İtfaiyeleri veritabanından çek
        $waterSources = WaterSource::all(); // Su kaynaklarını veritabanından çek

        $stats = [
            'active_incidents' => $allIncidents->count(),
            'dispatched_vehicles' => $allIncidents->sum('response_time_minutes'),
            'high_risk' => $allIncidents->whereIn('severity', ['Kritik', 'Yüksek'])->count(),
        ];
        
        $incidentsByRegion = $allIncidents->groupBy('location');

        // View'a tüm bu dinamik verileri gönderiyoruz
        return view('bakanlik', [
            'stats' => $stats,
            'incidentsByRegion' => $incidentsByRegion,
            'allIncidents' => $allIncidents,
            'fireStations' => $fireStations,
            'waterSources' => $waterSources,
        ]);
    }
}