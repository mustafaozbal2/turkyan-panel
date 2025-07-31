<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // KPI Kartları için verileri hesapla
        $totalIncidentsLast30Days = Incident::where('created_at', '>=', now()->subDays(30))->count();
        $avgResponseTime = Incident::avg('response_time_minutes');

        $mostActiveRegionQuery = Incident::select('location', DB::raw('count(*) as total'))
                            ->groupBy('location')
                            ->orderBy('total', 'desc')
                            ->first();
        $mostActiveRegion = $mostActiveRegionQuery ? $mostActiveRegionQuery->location : 'N/A';

        // Grafik verilerini hesapla
        $severityChartData = Incident::select('severity', DB::raw('count(*) as total'))
                            ->groupBy('severity')
                            ->pluck('total', 'severity');

        $regionChartData = Incident::select('location', DB::raw('count(*) as total'))
                            ->groupBy('location')
                            ->pluck('total', 'location');

        // View'a tüm hesaplanmış verileri gönder
        return view('raporlar', [
            'totalIncidents' => $totalIncidentsLast30Days,
            'avgResponseTime' => $avgResponseTime,
            'mostActiveRegion' => $mostActiveRegion,
            'severityChartData' => $severityChartData,
            'regionChartData' => $regionChartData,
        ]);
    }
}