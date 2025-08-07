<?php

namespace App\Http\Controllers;

use App\Models\FireStation;
use App\Models\WaterSource;
use Illuminate\Http\Request;

class HaritaController extends Controller
{
    public function index()
    {
        // Harita için gerekli olan itfaiye istasyonları ve su kaynaklarını
        // veritabanından çekiyoruz.
        $fireStations = FireStation::all();
        $waterSources = WaterSource::all();

        // Bu verileri 'harita' view'ına bir dizi olarak gönderiyoruz.
        return view('harita', [
            'fireStations' => $fireStations,
            'waterSources' => $waterSources,
        ]);
    }
}
