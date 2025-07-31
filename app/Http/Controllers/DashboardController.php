<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeIncidents = Incident::latest()->take(10)->get(); // Son 10 olayı alalım

        $incidentsForMap = $activeIncidents->map(function ($incident) {
            return [
                'lat' => $incident->latitude,
                'lng' => $incident->longitude,
                'time' => $incident->created_at->format('d.m.Y H:i'),
                'area' => $incident->area_hectares . ' Hektar',
                'status' => $incident->severity,
                'reqV' => 10,
                'onV' => 8,
                'risk' => $incident->severity === 'Kritik' ? 'Çok Yüksek' : ($incident->severity === 'Yüksek' ? 'Yüksek' : 'Orta')
            ];
        });

        // View'a verileri gönderiyoruz
        return view('index', [
            'incidentsForMap' => $incidentsForMap
        ]);
    }
}