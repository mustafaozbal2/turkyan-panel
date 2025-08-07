<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    /**
     * Aktif olayları GeoJSON formatında döndürür.
     */
    public function getActiveIncidentsAsGeoJson()
    {
        $incidents = Incident::latest()->get();

        $features = $incidents->map(function ($incident) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $incident->id,
                    'status' => $incident->severity, // veya 'status' sütunu
                    'area' => $incident->area_hectares . ' Hektar',
                    'name' => $incident->name,
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$incident->longitude, $incident->latitude],
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}