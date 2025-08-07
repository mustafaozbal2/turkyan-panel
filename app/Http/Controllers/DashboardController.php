<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\VolunteerReport;
use App\Models\FireStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\IncidentManagementService;
use App\Events\IncidentApproved;

class DashboardController extends Controller
{
    protected $incidentService;

    public function __construct(IncidentManagementService $incidentService)
    {
        $this->incidentService = $incidentService;
    }

    public function index()
    {
        $user = Auth::user();

        // Onay bekleyen AI tespitlerini al (bunlar genellikle herkese gösterilir)
        $pendingIncidents = Incident::where('status', 'onay_bekliyor')->latest()->get();

        // --- YENİ VE GELİŞTİRİLMİŞ GÖNÜLLÜ İHBAR MANTIĞI ---
        $pendingVolunteerReports = collect(); // Boş bir koleksiyonla başla

        // Giriş yapan kullanıcının bir istasyona bağlı olup olmadığını kontrol et
        $userStation = FireStation::where('user_id', $user->id)->first();

        if ($userStation) {
            // Tüm onay bekleyen gönüllü ihbarlarını al
            $allPendingReports = VolunteerReport::where('status', 'onay_bekliyor')->with('user')->get();

            foreach ($allPendingReports as $report) {
                // Her rapor için en yakın istasyonu bul
                // DÜZELTME: Mass assignment'ı atlatmak için özellikleri manuel olarak atıyoruz.
                $tempIncident = new Incident();
                $tempIncident->latitude = $report->latitude;
                $tempIncident->longitude = $report->longitude;
                
                $nearestStation = $this->incidentService->findNearestStation($tempIncident);

                // Eğer en yakın istasyon, bu giriş yapmış kullanıcının istasyonu ise, ihbarı listeye ekle
                if ($nearestStation && $nearestStation->id === $userStation->id) {
                    $pendingVolunteerReports->push($report);
                }
            }
        } 
        // Eğer giriş yapan kullanıcı Admin ise, coğrafi filtreleme yapma, tüm ihbarları göster
        elseif ($user->role === 'admin') {
            $pendingVolunteerReports = VolunteerReport::where('status', 'onay_bekliyor')->with('user')->latest()->get();
        }

        // Aktif olayları al
        $activeIncidents = Incident::where('status', 'aktif')->latest()->take(10)->get();

        $incidentsForMap = $activeIncidents->map(function ($incident) {
            return [
                'lat' => $incident->latitude,
                'lng' => $incident->longitude,
                'time' => $incident->created_at->format('d.m.Y H:i'),
                'area' => $incident->area_hectares . ' Hektar',
                'status' => $incident->severity,
                'name' => $incident->name,
            ];
        });

        return view('index', [
            'pendingIncidents' => $pendingIncidents,
            'pendingVolunteerReports' => $pendingVolunteerReports, // Artık filtrelenmiş ve akıllı liste
            'activeIncidents' => $activeIncidents,
            'incidentsForMap' => $incidentsForMap,
            'openWeatherApiKey' => env('OPENWEATHER_API_KEY')
        ]);
    }

    public function handleIncidentAction(Request $request, Incident $incident)
    {
        // Bu metodun içeriği aynı kalıyor
        $validated = $request->validate([
            'action' => 'required|string|in:approve,reject'
        ]);

        if ($validated['action'] === 'approve') {
            $incident->status = 'aktif';
            $incident->save();
            Log::info("Olay ID {$incident->id} onaylandı.");

            $nearestStation = $this->incidentService->findNearestStation($incident);
            $nearestWaterSource = $this->incidentService->findNearestWaterSource($incident);

            $stationName = $nearestStation ? $nearestStation->name : 'Bulunamadı';
            $sourceInfo = $nearestWaterSource ? "{$nearestWaterSource->name} ({$nearestWaterSource->distance} km)" : 'Bulunamadı';

            if ($nearestStation && $nearestStation->user) {
                $nearestStation->user->notify(new \App\Notifications\IncidentAlertNotification($incident));
                Log::info("Olay ID {$incident->id} için {$stationName} istasyonundaki kullanıcıya bildirim gönderildi.");
            } else {
                Log::warning("Olay ID {$incident->id} için yakınlarda bir istasyon veya istasyona atanmış kullanıcı bulunamadı!");
            }

            event(new IncidentApproved($incident));

            return redirect()->route('index')->with('success', "Olay onaylandı! En yakın istasyon: {$stationName}. En yakın su kaynağı: {$sourceInfo}.");
        }
        
        if ($validated['action'] === 'reject') {
            $incident->status = 'hatali_alarm';
            $incident->save();
            Log::info("Olay ID {$incident->id} hatalı alarm olarak işaretlendi.");
            return redirect()->route('index')->with('info', 'Gönüllü ihbarı reddedildi.');
        }

        return redirect()->route('index')->with('error', 'Geçersiz işlem.');
    }
}
