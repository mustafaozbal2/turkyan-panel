<?php

namespace App\Http\Controllers;

use App\Models\Incident; // Incident modelini kullanacağız
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // İstatistikler için DB sınıfını kullanacağız

class BakanlikController extends Controller
{
    public function index()
    {
        // Veritabanından tüm olayları çekiyoruz
        $allIncidents = Incident::latest()->get();

        // Ulusal Durum Raporu (KPI) için istatistikleri hesaplıyoruz
        $stats = [
            'active_incidents' => $allIncidents->count(),
            'dispatched_vehicles' => $allIncidents->sum('response_time_minutes'), // Örnek olarak müdahale süresini topluyoruz, bu daha sonra araç sayısıyla değiştirilebilir.
            'high_risk' => $allIncidents->whereIn('severity', ['Kritik', 'Yüksek'])->count(),
        ];

        // Olayları bölgelerine göre grupluyoruz
        $incidentsByRegion = $allIncidents->groupBy('location');

        // View'a tüm bu işlenmiş verileri gönderiyoruz
        return view('bakanlik', [
            'stats' => $stats,
            'incidentsByRegion' => $incidentsByRegion,
            'allIncidents' => $allIncidents
        ]);
    }
}