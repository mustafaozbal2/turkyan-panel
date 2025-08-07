<?php

namespace App\Services;

use App\Models\FireStation;
use App\Models\Incident;
use App\Models\WaterSource; // WaterSource modelini dahil ediyoruz
use Illuminate\Support\Facades\Log;

class IncidentManagementService
{
    /**
     * Verilen bir olaya en yakın itfaiye istasyonunu bulur.
     *
     * @param Incident $incident
     * @return FireStation|null
     */
    public function findNearestStation(Incident $incident): ?FireStation
    {
        $stations = FireStation::all();
        $nearestStation = null;
        $shortestDistance = INF; // Başlangıçta en kısa mesafeyi sonsuz olarak ayarla

        foreach ($stations as $station) {
            $distance = $this->calculateDistance(
                $incident->latitude,
                $incident->longitude,
                $station->latitude,
                $station->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestStation = $station;
            }
        }

        // En yakın istasyonun bilgilerini ve mesafesini loglayalım
        if ($nearestStation) {
            Log::info(
                "Olay ID {$incident->id} için en yakın istasyon bulundu: " .
                "{$nearestStation->name} (ID: {$nearestStation->id}), " .
                "Mesafe: " . round($shortestDistance, 2) . " km"
            );
        }

        return $nearestStation;
    }

    /**
     * YENİ METOD: Verilen bir olaya en yakın su kaynağını bulur.
     *
     * @param Incident $incident
     * @return object|null
     */
    public function findNearestWaterSource(Incident $incident): ?object
    {
        $waterSources = WaterSource::all();
        $nearestSource = null;
        $shortestDistance = INF;

        foreach ($waterSources as $source) {
            $distance = $this->calculateDistance(
                $incident->latitude,
                $incident->longitude,
                $source->latitude,
                $source->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestSource = $source;
            }
        }

        if ($nearestSource) {
            // Dönen nesneye mesafe bilgisini de ekleyelim
            return (object) [
                'name' => $nearestSource->name,
                'type' => $nearestSource->type,
                'distance' => round($shortestDistance, 2)
            ];
        }

        return null;
    }

    /**
     * İki coğrafi koordinat arasındaki mesafeyi Haversine formülü ile kilometre cinsinden hesaplar.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Dünya'nın yarıçapı (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Sonuç kilometre cinsinden
    }
}
