@extends('layouts.app')

@section('title', 'Bakanlık Stratejik Komuta Merkezi')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <style>
        body { font-family: 'Poppins', sans-serif; overflow: hidden; }
        .dashboard-panel { background-color: rgba(28, 28, 32, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        #main-view { height: calc(100vh - 60px); }
        .incident-list-item { transition: all 0.2s ease; border-right: 4px solid transparent; }
        .incident-list-item:hover { background-color: rgba(249, 115, 22, 0.1); border-right-color: #F97316; }
        .incident-list-item.active { background-color: rgba(249, 115, 22, 0.2); border-right-color: #F97316; }
        #incident-list-container::-webkit-scrollbar { width: 6px; }
        #incident-list-container::-webkit-scrollbar-track { background: transparent; }
        #incident-list-container::-webkit-scrollbar-thumb { background: #4A5568; border-radius: 3px; }
        .custom-popup .leaflet-popup-content-wrapper { background: #1f2937; color: #f3f4f6; border-radius: 8px; border: 1px solid #F97316; box-shadow: 0 5px 15px rgba(0,0,0,0.4); }
        .custom-popup .leaflet-popup-close-button { color: #f3f4f6; }
        .custom-popup .leaflet-popup-tip { background: #1f2937; }
        .popup-button { background-color: #4B5563; color: white; padding: 8px 12px; border-radius: 6px; text-decoration: none; transition: background-color 0.2s; }
        .popup-button:hover { background-color: #F97316; }
        .marker-cluster-small div, .marker-cluster-medium div, .marker-cluster-large div { background-color: rgba(28, 28, 28, 0.8); color: #fff; }
        .marker-cluster-small, .marker-cluster-medium, .marker-cluster-large { background-color: rgba(249, 115, 22, 0.2); border: 2px solid #F97316; }
    </style>
@endpush

@section('content')
<div id="main-view" class="w-full flex text-white">

    <aside id="incident-sidebar" class="w-1/3 max-w-sm h-full bg-gray-900/70 backdrop-blur-md border-r border-gray-700 flex flex-col">
        <div class="p-4 border-b border-gray-700">
            <h2 class="text-xl font-bold">Ulusal Durum Raporu</h2>
        </div>

        {{-- İstatistikler Artık Dinamik --}}
        <div class="grid grid-cols-3 gap-px bg-gray-700">
            <div class="bg-gray-800 p-3 text-center">
                <p class="text-xs text-gray-400">Aktif Olay</p>
                <p id="stats-active-incidents" class="text-2xl font-bold text-red-500">{{ $stats['active_incidents'] }}</p>
            </div>
            <div class="bg-gray-800 p-3 text-center">
                <p class="text-xs text-gray-400">Sevk Edilen Araç</p>
                <p id="stats-dispatched-vehicles" class="text-2xl font-bold text-blue-400">{{ $stats['dispatched_vehicles'] }}</p>
            </div>
            <div class="bg-gray-800 p-3 text-center">
                <p class="text-xs text-gray-400">Yüksek Riskli Olay</p>
                <p id="stats-high-risk" class="text-2xl font-bold text-orange-400">{{ $stats['high_risk'] }}</p>
            </div>
        </div>

        <div class="p-4 border-b border-gray-700">
            <input type="text" id="incident-search" placeholder="Olay veya bölge ara..." class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        {{-- Olay Listesi Artık Dinamik --}}
        <div id="incident-list-container" class="flex-grow overflow-y-auto">
            @foreach($incidentsByRegion as $regionName => $incidents)
            <div class="p-2">
                <h3 class="font-bold text-gray-400 text-sm px-2 sticky top-0 bg-gray-900/80 py-1">{{ strtoupper($regionName) }}</h3>
                @foreach($incidents as $incident)
                    @php
                        $color = $incident->severity === 'Kritik' ? 'red-500' : ($incident->severity === 'Yüksek' ? 'orange-500' : 'yellow-500');
                    @endphp
                    <div id="incident-{{ $incident->id }}" class="incident-list-item cursor-pointer p-2 rounded-md border-r-4 border-transparent" 
                         onclick="focusOnIncident({{ $incident->id }}, [{{ $incident->latitude }}, {{ $incident->longitude }}])">
                        <div class="flex justify-between">
                            <span class="font-semibold text-sm">{{ $incident->name }}</span>
                            <span class="text-xs text-{{ $color }} font-bold">{{ $incident->severity }}</span>
                        </div>
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
        const allIncidents = @json($allIncidents);

        const savedStateJSON = sessionStorage.getItem('mapState');
        let initialCenter = [39.0, 35.0], initialZoom = 6;
        if (savedStateJSON) { const s = JSON.parse(savedStateJSON); initialCenter = [s.lat, s.lng]; initialZoom = s.zoom; }

        const map = L.map('map', { center: initialCenter, zoom: initialZoom, zoomControl: false, attributionControl: false });
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
        L.control.zoom({ position: 'bottomright' }).addTo(map);
        map.on('moveend zoomend', () => sessionStorage.setItem('mapState', JSON.stringify({ lat: map.getCenter().lat, lng: map.getCenter().lng, zoom: map.getZoom() })));

        const fireIcon = (severity) => { const color = severity === 'Kritik' ? '#ef4444' : severity === 'Yüksek' ? '#f97316' : '#facc15'; return L.divIcon({ html: `<i class="fas fa-fire-alt text-2xl" style="color:${color}; text-shadow:0 0 8px #000;"></i>`, className:'border-0 bg-transparent', iconSize:[24,24]}); };

        const incidentLayer = L.layerGroup();
        const incidentMarkers = {};

        // Veriyi Controller'dan gelen `allIncidents` değişkeninden alıyoruz
        allIncidents.forEach(incident => {
            const marker = L.marker([incident.latitude, incident.longitude], { icon: fireIcon(incident.severity) }).addTo(incidentLayer);
            incidentMarkers[incident.id] = marker;
        });
        map.addLayer(incidentLayer);

        // Statik varlıkları (itfaiye vb.) hala yerel dosyadan çekiyoruz, bu en hızlı yöntem.
        async function loadAssets() {
            try {
                const response = await fetch("{{ asset('data/turkey-assets.geojson') }}");
                const assetsData = await response.json();
                const stationLayer = L.markerClusterGroup();
                const stations = L.geoJSON(assetsData, {
                    filter: (feature) => feature.properties.amenity === 'fire_station',
                    onEachFeature: (feature, layer) => {
                        const props = feature.properties;
                        const popupContent = `<div class="font-sans"><h4 class="font-bold text-base mb-2 text-orange-400">${props.name || 'İsimsiz'}</h4><p class="text-sm"><span class="font-semibold">Telefon:</span> ${props.phone || 'Belirtilmemiş'}</p><div class="mt-3 flex space-x-2"><a href="tel:${props.phone || ''}" class="popup-button flex-1 text-center text-xs"><i class="fas fa-phone-alt mr-1"></i>Ara</a><a href="#" class="popup-button flex-1 text-center text-xs"><i class="fas fa-comment-dots mr-1"></i>Mesaj</a></div></div>`;
                        layer.bindPopup(popupContent, { className: 'custom-popup', minWidth: 200 });
                    }
                });
                stationLayer.addLayer(stations);
                map.addLayer(stationLayer);
            } catch(e) { console.error("Varlık verisi yüklenirken hata oluştu:", e); }
        }

        window.focusOnIncident = function(id, coords) {
            map.flyTo(coords, 12);
            document.querySelectorAll('.incident-list-item').forEach(el => el.classList.remove('active'));
            document.getElementById(`incident-${id}`).classList.add('active');
        }

        loadAssets();
    });
    </script>
@endpush