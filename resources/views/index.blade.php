@extends('layouts.app')

@section('title', 'Ana Sayfa - TÜRKYAN Komuta Merkezi')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .dashboard-panel { background-color: rgba(28, 28, 32, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; display: flex; flex-direction: column; }
        .dashboard-panel:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); border-color: rgba(249, 115, 22, 0.4); }
        .panel-header { position: relative; padding-bottom: 0.75rem; flex-shrink: 0; }
        .panel-header::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background-color: #F97316; border-radius: 2px; }
        .status-badge { transition: background-color 0.3s ease, color 0.3s ease; }
        .weather-icon-svg { width: 80px; height: 80px; }
        #wind-compass-arrow { transition: transform 0.5s ease-out; }
    </style>
@endpush

@section('content')
    <main class="p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Sol Panel: Bölge Durumu --}}
        <div class="dashboard-panel rounded-xl shadow-lg p-6 space-y-5 lg:col-span-1">
            <h2 class="panel-header text-xl font-bold text-white">Bölge Durumu</h2>
            <div class="text-center bg-gray-900/50 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-400 mb-1">Seçilen Konum Durumu</p>
                <span id="fire-status" class="status-badge inline-block px-5 py-2 rounded-full text-lg font-semibold bg-gray-500 text-white">Veri Yok</span>
            </div>
            <div class="space-y-3 text-gray-300">
                <div class="bg-gray-900/50 p-3 rounded-md flex justify-between items-center"><span class="font-medium text-gray-400">Koordinat:</span><span id="fire-coords" class="font-semibold text-white">--</span></div>
                <div class="bg-gray-900/50 p-3 rounded-md flex justify-between items-center"><span class="font-medium text-gray-400">Tespit Zamanı:</span><span id="detection-time" class="font-semibold text-white">--</span></div>
                <div class="bg-gray-900/50 p-3 rounded-md flex justify-between items-center"><span class="font-medium text-gray-400">Tahmini Alan:</span><span id="fire-area" class="font-bold text-orange-400">--</span></div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-white mb-2">Müdahale Planı</h3>
                <div class="space-y-3 text-gray-300">
                    <p><span class="font-medium text-gray-400">Gerekli Araç:</span> <span id="req-vehicles" class="font-semibold text-white float-right">0</span></p>
                    <p><span class="font-medium text-gray-400">Yolda Olan Araç:</span> <span id="onway-vehicles" class="font-semibold text-white float-right">0</span></p>
                    <div class="w-full bg-gray-700 rounded-full h-2.5 mt-2"><div id="vehicles-progress" class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" style="width: 0%;"></div></div>
                </div>
            </div>
        </div>

        {{-- Orta Alan: Görüntü ve Harita --}}
        <div class="lg:col-span-2 flex flex-col gap-6">
            <div id="camera-container" class="dashboard-panel rounded-xl shadow-lg relative overflow-hidden flex-grow flex flex-col justify-center items-center min-h-[40vh]">
                <video id="camera-feed" autoplay playsinline class="w-full h-full object-cover"></video>
                <div id="camera-status-overlay" class="absolute inset-0 bg-black bg-opacity-70 flex items-center justify-center text-center p-4"><p class="text-white text-lg"><i class="fas fa-spinner fa-spin mr-2"></i>Kamera başlatılıyor...</p></div>
                <div class="absolute top-0 left-0 bg-black bg-opacity-50 text-white px-4 py-2 rounded-br-xl z-10"><span class="font-bold">GÖZETLEME GÖRÜNTÜSÜ</span></div>
                <div class="absolute bottom-4 flex space-x-3 z-10">
                    <button id="fullscreen-btn" class="bg-gray-800/50 hover:bg-gray-700/70 backdrop-blur-sm text-white font-semibold py-2 px-4 rounded-full shadow-md transition-colors duration-200 border border-gray-600"><i class="fas fa-expand mr-2"></i>Tam Ekran</button>
                </div>
            </div>
            <div class="dashboard-panel rounded-xl shadow-lg p-4 relative flex-grow">
                <h2 class="text-lg font-bold text-white mb-3">Operasyon Haritası</h2>
                <div id="map" class="w-full h-full min-h-[300px] rounded-lg"></div>
                <a href="{{ url('/harita') }}" class="absolute top-4 right-4 bg-gray-800/50 hover:bg-orange-600/70 backdrop-blur-sm text-white font-semibold py-2 px-4 rounded-full shadow-md transition-colors duration-200 border border-gray-600"><i class="fas fa-external-link-alt mr-2"></i>Detaylı Görünüm</a>
            </div>
        </div>
        
        {{-- Sağ Panel: Anlık Analiz --}}
        <div class="dashboard-panel rounded-xl shadow-lg p-6 space-y-5 lg:col-span-1">
             <div id="weather-loader" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-xl"><i class="fas fa-spinner fa-spin text-3xl text-white"></i></div>
            <h2 class="panel-header text-xl font-bold text-white">Anlık Analiz (<span id="weather-location">--</span>)</h2>
            <div class="flex-grow flex flex-col justify-around">
                <div class="text-center"><div id="weather-icon-container" class="mx-auto h-[80px] flex items-center justify-center"></div><p class="font-bold text-2xl capitalize text-white" id="weather-desc-text">--</p></div>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-gray-900/50 p-3 rounded-md"><p class="text-sm text-gray-400">Sıcaklık</p><p class="text-2xl font-semibold text-white" id="weather-temp">--</p></div>
                    <div class="bg-gray-900/50 p-3 rounded-md"><p class="text-sm text-gray-400">Nem</p><p class="text-2xl font-semibold text-white" id="weather-humidity">--</p></div>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-md text-center">
                    <p class="text-sm text-gray-400 mb-2">Rüzgar</p>
                    <div class="flex items-center justify-center">
                        <div id="wind-compass" class="relative w-16 h-16"><svg viewBox="0 0 100 100" class="fill-current text-gray-600"><circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4" fill="none"/><text x="50" y="18" text-anchor="middle" font-size="14" class="font-bold fill-current text-gray-500">K</text></svg><div id="wind-compass-arrow" class="absolute inset-0 flex items-center justify-center"><svg viewBox="0 0 100 100" class="w-10 h-10 fill-current text-orange-500"><path d="M50 0 L65 50 L50 40 L35 50 Z"/></svg></div></div>
                        <p class="text-2xl font-semibold text-white ml-4" id="weather-wind">--</p>
                    </div>
                </div>
                <div class="bg-gray-900/50 p-4 rounded-md text-center">
                    <p class="text-sm text-gray-400">Mevcut Risk</p><p id="fire-risk" class="text-2xl font-bold text-yellow-400">--</p></div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    const OWM_API_KEY = 'c7f5d2cf86c25eb85b206e27995e181c';
    
    // Controller'dan gelen hazır veriyi alıyoruz.
    const incidentsForMap = @json($incidentsForMap);

    const ui = {
        weather: { location: document.getElementById('weather-location'), descText: document.getElementById('weather-desc-text'), iconContainer: document.getElementById('weather-icon-container'), temp: document.getElementById('weather-temp'), wind: document.getElementById('weather-wind'), humidity: document.getElementById('weather-humidity'), loader: document.getElementById('weather-loader'), windArrow: document.getElementById('wind-compass-arrow') },
        fire: { time: document.getElementById('detection-time'), coords: document.getElementById('fire-coords'), area: document.getElementById('fire-area'), status: document.getElementById('fire-status'), reqVehicles: document.getElementById('req-vehicles'), onwayVehicles: document.getElementById('onway-vehicles'), progress: document.getElementById('vehicles-progress'), risk: document.getElementById('fire-risk') }
    };
    
    const weatherIcons = { "01": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><circle cx="32" cy="32" r="11" fill="#facc15"/><path d="M32 15V9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M32 55V49" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34 20.34L16.1 16.1" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M47.9 47.9L43.66 43.66" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M15 32L9 32" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M55 32L49 32" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34 43.66L16.1 47.9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M47.9 16.1L43.66 20.34" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/></g></svg>`, "02": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><circle cx="32" cy="32" r="11" fill="#facc15"/><path d="M32,15V9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34,20.34l-4.24-4.24" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M15,32H9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/></g></svg>`, "03": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/></svg>`, "04": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-width="3"/><path d="M35.66,25.2A11,11,0,0,0,35,23a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M13,12a11,11,0,0,0,0,22H33a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/></svg>`, "09": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></g><path d="M28 49L26 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M36 49L34 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "10": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M24,23a11,11,0,0,0,0,22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><circle cx="32" cy="32" r="11" fill="#facc15"/></g><path d="M28 49L26 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "11": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M30 49L26 57L34 57L32 63" fill="none" stroke="#f59e0b" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "13": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M32 49L32 55" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M26.29 50.41L37.71 53.59" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M26.29 53.59L37.71 50.41" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "50": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M17,39H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M17,45H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M17,51H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>` };
    
    async function getWeather(lat, lon) { if (!OWM_API_KEY.startsWith('c7f')) return; ui.weather.loader.style.display = 'flex'; try { const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${OWM_API_KEY}&units=metric&lang=tr`); if (!response.ok) throw new Error('Hata'); const data = await response.json(); updateWeatherUI(data); } catch (error) { console.error("Hava durumu hatası:", error); } finally { ui.weather.loader.style.display = 'none'; } }
    function updateWeatherUI(data) { const iconCode = data.weather[0].icon.slice(0, 2); ui.weather.location.textContent = data.name || "Bilinmeyen"; ui.weather.descText.textContent = data.weather[0].description; ui.weather.iconContainer.innerHTML = weatherIcons[iconCode] || weatherIcons["01"]; ui.weather.temp.textContent = `${Math.round(data.main.temp)}°C`; ui.weather.wind.textContent = `${(data.wind.speed * 3.6).toFixed(1)} km/s`; ui.weather.humidity.textContent = `${data.main.humidity}%`; ui.weather.windArrow.style.transform = `rotate(${data.wind.deg}deg)`; }
    function checkFire(lat, lng, mapInstance) { let fireFound = false; for (const incident of incidentsForMap) { if (mapInstance.distance(L.latLng(incident.lat, incident.lng), L.latLng(lat, lng)) < 25000) { updateFireUI(incident, lat, lng); fireFound = true; break; } } if (!fireFound) updateFireUI(null, lat, lng); }
    function updateFireUI(fireData, clickedLat, clickedLng) { ui.fire.coords.textContent = `${clickedLat.toFixed(4)} N, ${clickedLng.toFixed(4)} E`; if (fireData) { ui.fire.time.textContent = fireData.time; ui.fire.area.textContent = fireData.area; ui.fire.status.textContent = fireData.status; ui.fire.reqVehicles.textContent = fireData.reqV; ui.fire.onwayVehicles.textContent = fireData.onV; ui.fire.progress.style.width = `${(fireData.onV / fireData.reqV) * 100}%`; ui.fire.risk.textContent = fireData.risk; const statusClasses = { "Tehlikeli": "bg-red-500", "Kontrol Altında": "bg-yellow-500", "Soğutma": "bg-blue-500" }; const riskClasses = { "Çok Yüksek": "text-red-500", "Orta": "text-yellow-400", "Düşük": "text-green-400" }; ui.fire.status.className = `status-badge inline-block px-5 py-2 rounded-full text-lg font-semibold text-white ${statusClasses[fireData.status] || 'bg-gray-500'}`; ui.fire.risk.className = `text-2xl font-bold ${riskClasses[fireData.risk] || 'text-gray-400'}`; } else { ui.fire.time.textContent = "Anlık"; ui.fire.area.textContent = "Tespit Yok"; ui.fire.status.textContent = "Normal"; ui.fire.reqVehicles.textContent = "0"; ui.fire.onwayVehicles.textContent = "0"; ui.fire.progress.style.width = '0%'; ui.fire.risk.textContent = 'Düşük'; ui.fire.status.className = 'status-badge inline-block px-5 py-2 rounded-full text-lg font-semibold text-white bg-green-500'; ui.fire.risk.className = 'text-2xl font-bold text-green-400'; } }
    async function startCamera() { const videoElement = document.getElementById('camera-feed'); const statusOverlay = document.getElementById('camera-status-overlay'); try { const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false }); videoElement.srcObject = stream; videoElement.onloadedmetadata = () => statusOverlay.style.display = 'none'; } catch (error) { console.error("Kamera Hatası:", error); statusOverlay.innerHTML = '<p class="text-red-500"><i class="fas fa-video-slash mr-2"></i>Kamera erişimi reddedildi veya kamera bulunamadı.</p>'; } }
    
    window.onload = () => {
        startCamera();
        const savedStateJSON = sessionStorage.getItem('mapState');
        let initialCoords = [37.0662, 37.3785];
        let initialZoom = 10;
        if (savedStateJSON) { const savedState = JSON.parse(savedStateJSON); initialCoords = [savedState.lat, savedState.lng]; initialZoom = savedState.zoom; }
        const map = L.map('map', {zoomControl: false}).setView(initialCoords, initialZoom);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
        let clickMarker = L.marker(initialCoords, { opacity: 0.7 }).addTo(map);
        incidentsForMap.forEach(incident => { L.circle([incident.lat, incident.lng], { color: 'red', fillColor: '#f03', fillOpacity: 0.2, radius: 15000 }).addTo(map); });
        map.on('click', function(e) { const { lat, lng } = e.latlng; clickMarker.setLatLng(e.latlng); getWeather(lat, lng); checkFire(lat, lng, map); });
        map.on('moveend zoomend', function() { sessionStorage.setItem('mapState', JSON.stringify({ lat: map.getCenter().lat, lng: map.getCenter().lng, zoom: map.getZoom() })); });
        getWeather(initialCoords[0], initialCoords[1]);
        checkFire(initialCoords[0], initialCoords[1], map);
        const cameraContainer = document.getElementById('camera-container');
        const fullscreenBtn = document.getElementById('fullscreen-btn');
        fullscreenBtn.addEventListener('click', () => { if (cameraContainer.requestFullscreen) cameraContainer.requestFullscreen() });
    };
    </script>
@endpush