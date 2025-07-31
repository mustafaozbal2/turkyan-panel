@extends('layouts.app')

@section('title', 'Operasyon Merkezi Haritası')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    {{-- Modern yazı tipini ekliyoruz --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Proje geneline yeni yazı tipini uyguluyoruz */
        #map-wrapper, #details-sidebar, #layer-control {
            font-family: 'Poppins', sans-serif;
        }

        #map-wrapper { position: relative; height: calc(100vh - 88px); }
        #map { height: 100%; width: 100%; background-color: #1E1E1E; }
        
        /* Geliştirilmiş Detay Paneli (Sidebar) Stilleri */
        #details-sidebar {
            position: absolute;
            transform: translateX(-100%); /* Başlangıçta ekranın dışında */
            left: 0; top: 0;
            width: 350px;
            height: 100%;
            background-color: rgba(18, 18, 18, 0.85); /* Hafif şeffaflık */
            backdrop-filter: blur(8px); /* Arka plana blur efekti */
            z-index: 1100;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); /* Yumuşak animasyon */
            border-right: 1px solid #4B5563;
            box-shadow: 10px 0 25px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            color: #E5E7EB;
        }
        #details-sidebar.open { transform: translateX(0); }
        #sidebar-header { padding: 1.25rem; border-bottom: 1px solid #374151; }
        #sidebar-content { padding: 1.25rem; overflow-y: auto; flex-grow: 1; }
        #sidebar-content h3 { font-size: 1.5rem; font-weight: 600; color: #F97316; margin-bottom: 1rem; }
        #sidebar-content p { margin-bottom: 0.75rem; line-height: 1.6; color: #D1D5DB; }
        #sidebar-content .data-label { font-weight: 500; color: #9CA3AF; }
        #close-sidebar-btn { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #9CA3AF; font-size: 1.5rem; cursor: pointer; transition: color 0.2s; }
        #close-sidebar-btn:hover { color: #fff; }

        /* Geliştirilmiş Katman Kontrolü */
        #layer-control {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            background-color: rgba(28, 28, 28, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: 1px solid #4A5568;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .layer-item { display: flex; align-items: center; not-first-child:mt-2; }
        .layer-item label { color: #D1D5DB; font-size: 0.875rem; margin-left: 0.75rem; cursor: pointer; }
        .layer-item input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            height: 20px;
            width: 36px;
            background-color: #4A5568;
            border-radius: 9999px;
            position: relative;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        .layer-item input[type="checkbox"]::before {
            content: '';
            position: absolute;
            height: 16px;
            width: 16px;
            background-color: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: transform 0.2s ease-in-out;
        }
        .layer-item input:checked { background-color: #F97316; }
        .layer-item input:checked::before { transform: translateX(16px); }

        /* Geliştirilmiş Yükleme Göstergesi */
        #map-loader {
            position: absolute;
            bottom: 1.25rem;
            right: 5.5rem;
            z-index: 1000;
            background: rgba(28, 28, 28, 0.85);
            backdrop-filter: blur(8px);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            display: block; /* Başlangıçta görünür */
            pointer-events: none;
            border: 1px solid #4A5568;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .svg-icon-shadow { filter: drop-shadow(0 2px 3px rgba(0,0,0,0.7)); }
        .marker-cluster-small div, .marker-cluster-medium div, .marker-cluster-large div { background-color: rgba(28, 28, 28, 0.8); color: #fff; }
        .marker-cluster-small, .marker-cluster-medium, .marker-cluster-large { background-color: rgba(249, 115, 22, 0.2); border: 2px solid #F97316; }
    </style>
@endpush

@section('content')
<div id="map-wrapper">
    <div id="map"></div>
    <div id="details-sidebar">
        <div id="sidebar-header">
            <button id="close-sidebar-btn" title="Paneli Kapat">&times;</button>
        </div>
        <div id="sidebar-content">
            {{-- İçerik JavaScript ile doldurulacak --}}
        </div>
    </div>
    <div id="layer-control">
        <div class="layer-item">
            <input type="checkbox" id="toggle-stations" checked>
            <label for="toggle-stations">İtfaiye Merkezleri</label>
        </div>
        <div class="layer-item mt-3">
            <input type="checkbox" id="toggle-water" checked>
            <label for="toggle-water">Su Kaynakları</label>
        </div>
    </div>
    <div id="map-loader"><i class="fas fa-spinner fa-spin mr-2"></i>Veri yükleniyor...</div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('details-sidebar');
        const sidebarContent = document.getElementById('sidebar-content');
        const mapLoader = document.getElementById('map-loader');
        
        const savedStateJSON = sessionStorage.getItem('mapState');
        let initialCenter = [39.0, 35.0], initialZoom = 6;
        if (savedStateJSON) { const savedState = JSON.parse(savedStateJSON); initialCenter = [savedState.lat, savedState.lng]; initialZoom = savedState.zoom; }

        const map = L.map('map', { center: initialCenter, zoom: initialZoom, zoomControl: false, attributionControl: false });
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        map.on('moveend zoomend', () => sessionStorage.setItem('mapState', JSON.stringify({ lat: map.getCenter().lat, lng: map.getCenter().lng, zoom: map.getZoom() })));
        
        document.getElementById('close-sidebar-btn').addEventListener('click', () => sidebar.classList.remove('open'));
        map.on('click', () => sidebar.classList.remove('open'));
        function openSidebar(content) {
            const header = sidebar.querySelector('#sidebar-header');
            header.innerHTML = `<button id="close-sidebar-btn" title="Paneli Kapat">&times;</button>`; // Close butonu tekrar ekleniyor
            document.getElementById('close-sidebar-btn').addEventListener('click', () => sidebar.classList.remove('open'));
            sidebarContent.innerHTML = content;
            sidebar.classList.add('open');
        }

        const stationIcon = L.divIcon({ html: `<svg class="svg-icon-shadow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="32" height="32"><path fill="#27272a" d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/><path fill="#f97316" d="M12 3.19l7 3.11v4.7c0 4.52-2.98 8.68-7 9.93-4.02-1.25-7-5.41-7-9.93v-4.7l7-3.11z"/><path fill="white" d="M14.47 13.9L12 11.45l-2.47 2.45-.71-.71L11.29 10H9v-1h5v1h-2.29l3.18 3.18-.72.72zM12 6c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/></svg>`, className:'border-0 bg-transparent', iconSize:[32,32], iconAnchor: [16,32] });
        const waterIcon = L.divIcon({ html: `<svg class="svg-icon-shadow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="30" height="30"><path fill="#2563eb" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path fill="#3b82f6" d="M12 6c-2.28 0-4.72 1.93-4.72 4.2 0 1.62.95 3.12 2.37 3.72.68.28 1.45.04 1.88-.56.63-.88.24-2.15-1.03-2.65-.8-.32-1.7-.02-2.12.67-.18.3-.54.4-.84.22s-.4-.54-.22-.84c.66-1.09 2.06-1.55 3.31-1.12 1.45.51 2.23 2.08 1.76 3.56-.6 1.9-2.67 3-4.63 3 .15-1.5.89-2.8 1.88-3.79.52-.52 1.36-.51 1.87.01.73.73.22 1.95-1.02 2.2-1.18.25-2.21-.52-2.34-1.58-.04-.28-.3-.48-.59-.48s-.55.2-.59.48c-.2 1.68 1.12 3.1 2.8 3.1 2.28 0 4.72-1.93 4.72-4.2C16.72 7.93 14.28 6 12 6z"/></svg>`, className:'border-0 bg-transparent', iconSize:[30,30], iconAnchor: [15,30] });

        const stationLayer = L.markerClusterGroup({ chunkedLoading: true });
        const waterLayer = L.markerClusterGroup({ chunkedLoading: true });
        
        async function loadData() {
            try {
                const response = await fetch("{{ asset('data/turkey-assets.geojson') }}?v=" + new Date().getTime());
                if (!response.ok) throw new Error(`Veri dosyası yüklenemedi: ${response.statusText}`);
                const geojsonData = await response.json();
                
                const stations = L.geoJSON(geojsonData, {
                    filter: (feature) => feature.properties.amenity === 'fire_station',
                    pointToLayer: (feature, latlng) => L.marker(latlng, { icon: stationIcon }),
                    onEachFeature: (feature, layer) => {
                        const props = feature.properties;
                        layer.on('click', e => { L.DomEvent.stopPropagation(e); openSidebar(`<h3><i class="fas fa-building mr-3"></i>İtfaiye Merkezi</h3><p><span class="data-label">İsim:</span> ${props.name || 'İsimsiz'}</p>`); });
                    }
                });
                stationLayer.addLayer(stations);

                const water = L.geoJSON(geojsonData, {
                    filter: (feature) => feature.properties.natural === 'water',
                    pointToLayer: (feature, latlng) => L.marker(latlng, { icon: waterIcon }),
                    onEachFeature: (feature, layer) => {
                        const props = feature.properties;
                        layer.on('click', e => { L.DomEvent.stopPropagation(e); openSidebar(`<h3><i class="fas fa-tint mr-3"></i>Su Kaynağı</h3><p><span class="data-label">İsim:</span> ${props.name || 'İsimsiz'}</p><p><span class="data-label">Tipi:</span> ${props.water || props.natural || 'Bilinmiyor'}</p>`); });
                    }
                });
                waterLayer.addLayer(water);

                map.addLayer(stationLayer);
                map.addLayer(waterLayer);
            } catch(e) {
                console.error("Veri yüklenirken hata oluştu:", e);
                mapLoader.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> Veri Yüklenemedi!`;
            } finally {
                setTimeout(() => { mapLoader.style.display = 'none'; }, 500);
            }
        }
        
        document.getElementById('toggle-stations').addEventListener('change', e => e.target.checked ? map.addLayer(stationLayer) : map.removeLayer(stationLayer));
        document.getElementById('toggle-water').addEventListener('change', e => e.target.checked ? map.addLayer(waterLayer) : map.removeLayer(waterLayer));
        
        loadData();
    });
    </script>
@endpush