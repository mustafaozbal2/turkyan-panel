<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VolunteerReport;
use App\Models\Incident; // Eklendi
use Illuminate\Support\Facades\DB; // Eklendi
use Carbon\Carbon; // Eklendi


class ReportController extends Controller
{
    public function index()
    {
        // --- BU KISIM TAMAMEN YENİLENDİ ---

        // 1. Raporları al (Bu sayfanın amacına göre bu sorgu değişebilir, şimdilik tümünü alıyoruz)
        $reports = VolunteerReport::with('user')->latest()->get();

        // 2. İstatistik Kartları İçin Hesaplamalar
        $totalIncidents = Incident::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $avgResponseTime = Incident::where('response_time_minutes', '>', 0)->avg('response_time_minutes');
        $mostActive = DB::table('incidents')
                        ->select('location', DB::raw('count(*) as incident_count'))
                        ->groupBy('location')
                        ->orderByDesc('incident_count')
                        ->first();
        $mostActiveRegion = $mostActive ? $mostActive->location : 'Veri Yok';

        // 3. Grafik Verileri İçin Hesaplamalar
        $severityData = DB::table('incidents')
                         ->select('severity', DB::raw('count(*) as total'))
                         ->groupBy('severity')
                         ->pluck('total', 'severity');
        $severityChartData = [
            'labels' => $severityData->keys(),
            'data' => $severityData->values()
        ];

        $regionData = DB::table('incidents')
                        ->select('location', DB::raw('count(*) as total'))
                        ->groupBy('location')
                        ->orderByDesc('total')
                        ->limit(5)
                        ->pluck('total', 'location');
        $regionChartData = [
            'labels' => $regionData->keys(),
            'data' => $regionData->values()
        ];

        // 4. Tüm verileri toplu halde view'e gönder
        return view('raporlar', compact(
            'reports',
            'totalIncidents',
            'avgResponseTime',
            'mostActiveRegion',
            'severityChartData',
            'regionChartData'
        ));
    }
}