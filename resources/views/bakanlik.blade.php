@extends('layouts.app')

@section('title', 'Bakanlık Stratejik Komuta Merkezi')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; overflow: hidden; }
        #main-view { height: calc(100vh - 56px); } /* 56px navbar yüksekliği */
        .incident-list-item:hover { background-color: rgba(249, 115, 22, 0.1); }
        #incident-list-container::-webkit-scrollbar { width: 6px; }
        #incident-list-container::-webkit-scrollbar-track { background: transparent; }
        #incident-list-container::-webkit-scrollbar-thumb { background: #4A5568; border-radius: 3px; }
        .custom-popup .leaflet-popup-content-wrapper { background: #1f2937; color: #f3f4f6; border-radius: 8px; border: 1px solid #F97316; }
        .custom-popup .leaflet-popup-tip { background: #1f2937; }
        .popup-button { background-color: #4B5563; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none; transition: background-color 0.2s; }
        .popup-button:hover { background-color: #F97316; }
    </style>
@endpush

@section('content')
<div id="main-view" class="w-full flex text-white">
    
    <aside id="incident-sidebar" class="w-1/3 max-w-sm h-full bg-gray-900/70 backdrop-blur-md border-r border-gray-700 flex flex-col">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-bold">Ulusal Durum Raporu</h2>
        </div>
        <div class="grid grid-cols-3 gap-px bg-gray-700">
            <div class="bg-gray-800 p-3 text-center"><p class="text-xs text-gray-400">Aktif Olay</p><p class="text-2xl font-bold text-red-500">{{ $stats['active_incidents'] }}</p></div>
            <div class="bg-gray-800 p-3 text-center"><p class="text-xs text-gray-400">Sevk Edilen Araç</p><p class="text-2xl font-bold text-blue-400">{{ $stats['dispatched_vehicles'] }}</p></div>
            <div class="bg-gray-800 p-3 text-center"><p class="text-xs text-gray-400">Yüksek Riskli Olay</p><p class="text-2xl font-bold text-orange-400">{{ $stats['high_risk'] }}</p></div>
        </div>
        <div class="p-4 border-b border-gray-700"><input type="text" id="incident-search" placeholder="Olay veya bölge ara..." class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"></div>
        <div id="incident-list-container" class="flex-grow overflow-y-auto">
            @foreach($incidentsByRegion as $regionName => $incidents)
            <div class="p-2">
                <h3 class="font-bold text-gray-400 text-sm px-2 sticky top-0 bg-gray-900/80 py-1">{{ strtoupper($regionName) }}</h3>
                @foreach($incidents as $incident)
                    @php $color = $incident->severity === 'Kritik' ? 'red-500' : ($incident->severity === 'Yüksek' ? 'orange-500' : 'yellow-500'); @endphp
                    <div id="incident-{{ $incident->id }}" class="incident-list-item cursor-pointer p-2 rounded-md" onclick="focusOnIncident({{ $incident->id }}, [{{ $incident->latitude }}, {{ $incident->longitude }}])">
                        <div class="flex justify-between"><span class="font-semibold text-sm">{{ $incident->name }}</span><span class="text-xs text-{{ $color }} font-bold">{{ $incident->severity }}</span></div>
                        <div class="text-xs text-gray-400">{{ $incident->area_hectares }} Hektar</div>
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </aside>

    <main class="w-2/3 h-full flex-grow">
        <div id="map" class="w-full h-full"></div>
    </main>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- NİHAİ DÜZELTME: VERİLER ARTIK DOĞRUDAN CONTROLLER'DAN GELİYOR ---
        const allIncidents = @json($allIncidents);
        const fireStations = @json($fireStations);
        const waterSources = @json($waterSources);
        
        const map = L.map('map').setView([39.0, 35.0], 6);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);

        const fireIcon = (severity) => { const color = severity === 'Kritik' ? '#ef4444' : severity === 'Yüksek' ? '#f97316' : '#facc15'; return L.divIcon({ html: `<i class="fas fa-fire-alt text-2xl" style="color:${color}; text-shadow:0 0 8px #000;"></i>`, className:'border-0 bg-transparent', iconSize:[24,24]}); };
        const stationIcon = L.divIcon({ html: `<svg class="svg-icon-shadow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28"><path fill="#27272a" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/><path fill="#f97316" d="M12 3.19l7 3.11v4.7c0 4.52-2.98 8.68-7 9.93-4.02-1.25-7-5.41-7-9.93v-4.7l7-3.11z"/><path fill="white" d="M14.47 13.9L12 11.45l-2.47 2.45-.71-.71L11.29 10H9v-1h5v1h-2.29l3.18 3.18-.72.72zM12 6c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/></svg>`, className:'border-0 bg-transparent', iconSize:[28,28], iconAnchor: [14,28] });
        
        const stationLayer = L.markerClusterGroup();
        const incidentLayer = L.layerGroup();
        const incidentMarkers = {};
        
        // İtfaiyeleri veritabanından gelen dinamik veriyle yükle
        fireStations.forEach(station => {
            const marker = L.marker([station.latitude, station.longitude], {icon: stationIcon});
            const popupContent = `
                <div class="font-sans">
                    <h4 class="font-bold text-base mb-2 text-orange-400">${station.name || 'İsimsiz'}</h4>
                    <p class="text-sm"><span class="font-semibold">Telefon:</span> ${station.phone || 'Belirtilmemiş'}</p>
                    <div class="mt-3 flex space-x-2">
                        <a href="tel:${station.phone || ''}" class="popup-button flex-1 text-center text-xs"><i class="fas fa-phone-alt mr-1"></i>Ara</a>
                        <a href="/mesajlar/${station.user_id}" class="popup-button flex-1 text-center text-xs"><i class="fas fa-comment-dots mr-1"></i>Mesaj</a>
                    </div>
                </div>
            `;
            marker.bindPopup(popupContent, { className: 'custom-popup', minWidth: 200 });
            stationLayer.addLayer(marker);
        });

        // Olayları veritabanından gelen veriyle yükle
        allIncidents.forEach(incident => {
            const marker = L.marker([incident.latitude, incident.longitude], { icon: fireIcon(incident.severity) }).addTo(incidentLayer);
            incidentMarkers[incident.id] = marker;
        });

        map.addLayer(stationLayer);
        map.addLayer(incidentLayer);

        window.focusOnIncident = function(id, coords) {
            map.flyTo(coords, 12);
            if (incidentMarkers[id]) {
                incidentMarkers[id].openPopup();
            }
        }
    });
    </script>
@endpush