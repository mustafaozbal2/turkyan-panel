<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VolunteerReport;
use App\Models\FireStation; // Bu satırın ekli olduğundan emin olun

class VolunteerReportController extends Controller
{
    public function create()
    {
        return view('report.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
        ]);

        $imagePath = $request->file('image')->store('reports', 'public');

        $report = new VolunteerReport([
            'user_id' => auth()->id(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'image_path' => $imagePath,
            'description' => $request->description,
            'status' => 'onay_bekliyor', // Rapor direkt "onay bekliyor" olarak kaydedilir
        ]);
        $report->save();

        // --- YENİ EKLENEN ATAMA MANTIĞI BAŞLANGICI ---

        $this->assignToNearestStation($report);

        // --- YENİ EKLENEN ATAMA MANTIĞI SONU ---


        return redirect()->route('dashboard')->with('success', 'İhbarınız başarıyla alındı ve en yakın ekibe yönlendirildi.');
    }

    /**
     * Verilen raporu en yakın itfaiye istasyonuna atar.
     *
     * @param  \App\Models\VolunteerReport  $report
     * @return void
     */
    private function assignToNearestStation(VolunteerReport $report)
    {
        $stations = FireStation::all();
        $nearestStation = null;
        $shortestDistance = -1;

        foreach ($stations as $station) {
            // Haversine formülü ile mesafe hesaplaması
            $distance = $this->calculateDistance(
                $report->latitude,
                $report->longitude,
                $station->latitude,
                $station->longitude
            );

            if ($shortestDistance == -1 || $distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestStation = $station;
            }
        }

        // En yakın istasyon bulunduysa, raporu o istasyonun kullanıcısına ata
        if ($nearestStation && $nearestStation->user_id) {
            $report->assigned_user_id = $nearestStation->user_id;
            $report->save();
        }
    }

    /**
     * İki coğrafi koordinat arasındaki mesafeyi kilometre olarak hesaplar.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Mesafe (km)
    }
}