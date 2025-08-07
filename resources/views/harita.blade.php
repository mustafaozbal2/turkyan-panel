@extends('layouts.app')

@section('title', 'Varlık Haritası')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        #map-wrapper, #layer-control { font-family: 'Poppins', sans-serif; }
        #map-wrapper { position: relative; height: calc(100vh - 88px); }
        #map { height: 100%; width: 100%; background-color: #1E1E1E; }
        
        /* Katman Kontrol Paneli */
        #layer-control {
            position: absolute; top: 1rem; right: 1rem; z-index: 1000;
            background-color: rgba(28, 28, 28, 0.85); backdrop-filter: blur(8px);
            border-radius: 0.75rem; padding: 0.75rem; border: 1px solid #4A5568;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .layer-item { display: flex; align-items: center; }
        .layer-item:not(:first-child) { margin-top: 0.75rem; }
        .layer-item label { color: #D1D5DB; font-size: 0.875rem; margin-left: 0.75rem; cursor: pointer; }
        .layer-item input[type="checkbox"] {
            appearance: none; -webkit-appearance: none; height: 20px; width: 36px;
            background-color: #4A5568; border-radius: 9999px; position: relative;
            cursor: pointer; transition: background-color 0.2s ease-in-out;
        }
        .layer-item input[type="checkbox"]::before {
            content: ''; position: absolute; height: 16px; width: 16px;
            background-color: white; border-radius: 50%; top: 2px; left: 2px;
            transition: transform 0.2s ease-in-out;
        }
        .layer-item input:checked { background-color: #F97316; }
        .layer-item input:checked::before { transform: translateX(16px); }
        .svg-icon-shadow { filter: drop-shadow(0 2px 3px rgba(0,0,0,0.7)); }
        
        /* Karanlık tema için popup stilleri */
        .leaflet-popup-content-wrapper, .leaflet-popup-tip {
            background: #2d3748; /* Koyu gri */
            color: #e2e8f0; /* Açık gri yazı */
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.5);
        }
    </style>
@endpush

@section('content')
<div id="map-wrapper">
    <div id="map"></div>
    <div id="layer-control">
        <div class="layer-item">
            <input type="checkbox" id="toggle-stations" checked>
            <label for="toggle-stations">İtfaiye Merkezleri</label>
        </div>
        <div class="layer-item">
            <input type="checkbox" id="toggle-water" checked>
            <label for="toggle-water">Su Kaynakları</label>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- DEĞİŞKENLER VE VERİLER ---
        const fireStationsData = @json($fireStations);
        const waterSourcesData = @json($waterSources);

        // --- HARİTA KURULUMU ---
        // Beğendiğiniz modern ve şık karanlık temayı kullanıyoruz.
        const map = L.map('map').setView([39.0, 35.0], 6);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);

        // --- İKON TANIMLARI (Basitleştirilmiş) ---
        const stationIcon = L.divIcon({ html: `<i class="fas fa-building fa-2x text-orange-500 svg-icon-shadow"></i>`, className:'border-0 bg-transparent', iconSize:[24, 24], iconAnchor: [12, 24] });
        const waterIcon = L.divIcon({ html: `<i class="fas fa-tint fa-2x text-blue-500 svg-icon-shadow"></i>`, className:'border-0 bg-transparent', iconSize:[24, 24], iconAnchor: [12, 24] });
        
        // --- KATMANLARI OLUŞTURMA ---
        const stationLayer = L.layerGroup();
        const waterLayer = L.layerGroup();
        
        // İtfaiye istasyonlarını haritaya ekle (Sadece popup ile)
        fireStationsData.forEach(station => {
            const marker = L.marker([station.latitude, station.longitude], { icon: stationIcon });
            marker.bindPopup(`<b>${station.name || 'İsimsiz'}</b>`);
            stationLayer.addLayer(marker);
        });

        // Su kaynaklarını haritaya ekle (Sadece popup ile)
        waterSourcesData.forEach(source => {
            const marker = L.marker([source.latitude, source.longitude], { icon: waterIcon });
            marker.bindPopup(`<b>${source.name || 'İsimsiz'}</b><br>Tipi: ${source.type || 'Bilinmiyor'}`);
            waterLayer.addLayer(marker);
        });

        map.addLayer(stationLayer);
        map.addLayer(waterLayer);
        
        // --- KATMAN KONTROL BUTONLARI ---
        document.getElementById('toggle-stations').addEventListener('change', e => e.target.checked ? map.addLayer(stationLayer) : map.removeLayer(stationLayer));
        document.getElementById('toggle-water').addEventListener('change', e => e.target.checked ? map.addLayer(waterLayer) : map.removeLayer(waterLayer));
    });
    </script>
@endpush
