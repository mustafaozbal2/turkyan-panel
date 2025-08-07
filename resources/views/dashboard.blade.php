@extends('layouts.app')

@section('title', 'Gönüllü Paneli')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .dashboard-panel { background-color: rgba(28, 28, 32, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .panel-header { position: relative; padding-bottom: 0.75rem; }
        .panel-header::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background-color: #F97316; border-radius: 2px; }
        .weather-icon-svg { width: 64px; height: 64px; }
        #wind-compass-arrow { transition: transform 0.5s ease-out; }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    
   {{-- resources/views/dashboard.blade.php dosyasında --}}
<div id="welcome-message" class="mb-8">
    <div class="flex justify-between items-center">
        <div id="welcome-message" class="mb-8">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold">Hoş Geldin, <span class="text-orange-400">{{ Auth::user()->name }}</span>!</h1>
            <p class="text-gray-400 mt-2">Afet yönetimine destek olduğun için teşekkür ederiz.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('news.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-md transition-colors duration-300">
                <i class="fas fa-newspaper mr-2"></i>Haberleri Görüntüle
            </a>
            <!-- YENİ EKLENEN BUTON -->
            <a href="{{ route('volunteer.report.create') }}" class="bg-red-600 hover:bg-red-500 text-white font-bold py-3 px-6 rounded-md transition-colors duration-300 animate-pulse">
                <i class="fas fa-fire-extinguisher mr-2"></i>Yangın İhbar Et
            </a>
        </div>
    </div>
</div>

       
    </div>
</div>

    <div id="location-prompt" class="dashboard-panel rounded-xl p-8 text-center">
        <i class="fas fa-map-marker-alt text-6xl text-blue-500 mb-4"></i>
        <h2 class="text-2xl font-semibold">Yakındaki Olayları Göster</h2>
        <p class="text-gray-400 mt-2 max-w-md mx-auto">Size en yakın olayları ve bölgenin hava durumu risk analizini sunabilmemiz için konumunuza erişim izni vermeniz gerekmektedir.</p>
        <button id="get-location-btn" class="mt-6 bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 px-6 rounded-md transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-crosshairs mr-2"></i>Konumumu Bul ve Analiz Et
        </button>
    </div>

    <div id="main-content" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 flex flex-col gap-8">
            <div class="dashboard-panel rounded-xl p-6">
                <h2 class="panel-header text-xl font-bold text-white">Yakınındaki Olaylar (10km)</h2>
                <div id="incidents-list" class="mt-4 space-y-4">
                    {{-- Olay kartları buraya eklenecek --}}
                </div>
            </div>
            <div class="dashboard-panel rounded-xl p-4 flex-grow">
                <h2 class="text-lg font-bold text-white mb-3">Bölge Haritası</h2>
                <div id="map" class="w-full h-full min-h-[400px] rounded-lg"></div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="dashboard-panel rounded-xl p-6 space-y-5 sticky top-6">
                <h2 class="panel-header text-xl font-bold text-white">Konum Analizi (<span id="weather-location">--</span>)</h2>
                <div id="weather-loader" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-xl"><i class="fas fa-spinner fa-spin text-3xl text-white"></i></div>
                <div class="flex-grow flex flex-col justify-around space-y-4">
                    <div class="text-center"><div id="weather-icon-container" class="mx-auto h-[64px] flex items-center justify-center"></div><p class="font-bold text-xl capitalize text-white" id="weather-desc-text">--</p></div>
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="bg-gray-900/50 p-3 rounded-md"><p class="text-xs text-gray-400">Sıcaklık</p><p class="text-xl font-semibold text-white" id="weather-temp">--</p></div>
                        <div class="bg-gray-900/50 p-3 rounded-md"><p class="text-xs text-gray-400">Nem</p><p class="text-xl font-semibold text-white" id="weather-humidity">--</p></div>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-md text-center">
                        <p class="text-xs text-gray-400 mb-2">Rüzgar</p>
                        <div class="flex items-center justify-center">
                            <div id="wind-compass" class="relative w-12 h-12"><svg viewBox="0 0 100 100" class="fill-current text-gray-600"><circle cx="50" cy="50" r="48" stroke="currentColor" stroke-width="4" fill="none"/><text x="50" y="18" text-anchor="middle" font-size="14" class="font-bold fill-current text-gray-500">K</text></svg><div id="wind-compass-arrow" class="absolute inset-0 flex items-center justify-center"><svg viewBox="0 0 100 100" class="w-8 h-8 fill-current text-orange-500"><path d="M50 0 L65 50 L50 40 L35 50 Z"/></svg></div></div>
                            <p class="text-xl font-semibold text-white ml-3" id="weather-wind">--</p>
                        </div>
                    </div>
                    <div class="bg-gray-900/50 p-3 rounded-md text-center">
                        <p class="text-xs text-gray-400">Mevcut Risk</p><p id="fire-risk" class="text-xl font-bold text-yellow-400">--</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    const OWM_API_KEY = 'c7f5d2cf86c25eb85b206e27995e181c';
    const ui = {
        weather: { location: document.getElementById('weather-location'), descText: document.getElementById('weather-desc-text'), iconContainer: document.getElementById('weather-icon-container'), temp: document.getElementById('weather-temp'), wind: document.getElementById('weather-wind'), humidity: document.getElementById('weather-humidity'), loader: document.getElementById('weather-loader'), windArrow: document.getElementById('wind-compass-arrow') },
        fireRisk: document.getElementById('fire-risk'),
        incidentsList: document.getElementById('incidents-list')
    };
    const weatherIcons = { "01": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><circle cx="32" cy="32" r="11" fill="#facc15"/><path d="M32 15V9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M32 55V49" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34 20.34L16.1 16.1" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M47.9 47.9L43.66 43.66" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M15 32L9 32" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M55 32L49 32" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34 43.66L16.1 47.9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M47.9 16.1L43.66 20.34" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/></g></svg>`, "02": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><circle cx="32" cy="32" r="11" fill="#facc15"/><path d="M32,15V9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M20.34,20.34l-4.24-4.24" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/><path d="M15,32H9" fill="none" stroke="#facc15" stroke-linecap="round" stroke-width="3"/></g></svg>`, "03": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/></svg>`, "04": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-width="3"/><path d="M35.66,25.2A11,11,0,0,0,35,23a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/><path d="M13,12a11,11,0,0,0,0,22H33a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-width="3"/></svg>`, "09": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></g><path d="M28 49L26 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M36 49L34 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "10": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><g><path d="M24,23a11,11,0,0,0,0,22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><circle cx="32" cy="32" r="11" fill="#facc15"/></g><path d="M28 49L26 55" fill="none" stroke="#3b82f6" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "11": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M30 49L26 57L34 57L32 63" fill="none" stroke="#f59e0b" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "13": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M46.66,36.2A11,11,0,0,0,46,34a11,11,0,0,0-22,0,11,11,0,0,0,.66,2.2" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M24,23a11,11,0,0,0,0,22H44a11,11,0,0,0,0-22" fill="none" stroke="#e5e7eb" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M32 49L32 55" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M26.29 50.41L37.71 53.59" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M26.29 53.59L37.71 50.41" fill="none" stroke="#60a5fa" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`, "50": `<svg class="weather-icon-svg" viewBox="0 0 64 64"><path d="M17,39H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M17,45H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/><path d="M17,51H47" fill="none" stroke="#94a3b8" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/></svg>`};

    // --- YENİ EKLENEN FONKSİYONLAR ---
    async function getWeather(lat, lon) {
        if (!OWM_API_KEY.startsWith('c7f')) return;
        ui.weather.loader.style.display = 'flex';
        try {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${OWM_API_KEY}&units=metric&lang=tr`);
            if (!response.ok) throw new Error('Hava durumu verisi alınamadı.');
            const data = await response.json();
            updateWeatherUI(data);
        } catch (error) {
            console.error("Hava durumu hatası:", error);
            ui.weather.descText.textContent = "Veri Alınamadı";
        } finally {
            ui.weather.loader.style.display = 'none';
        }
    }
    
    function updateWeatherUI(data) {
        const iconCode = data.weather[0].icon.slice(0, 2);
        ui.weather.location.textContent = data.name || "Bilinmeyen Bölge";
        ui.weather.descText.textContent = data.weather[0].description;
        ui.weather.iconContainer.innerHTML = weatherIcons[iconCode] || weatherIcons["01"];
        ui.weather.temp.textContent = `${Math.round(data.main.temp)}°C`;
        ui.weather.wind.textContent = `${(data.wind.speed * 3.6).toFixed(1)} km/s`;
        ui.weather.humidity.textContent = `${data.main.humidity}%`;
        ui.weather.windArrow.style.transform = `rotate(${data.wind.deg}deg)`;
        
        // Basit bir risk analizi
        let risk = 'Düşük';
        let riskColor = 'text-green-400';
        if (data.wind.speed > 10) risk = 'Orta'; // 36 km/s'den fazla rüzgar
        if (data.wind.speed > 15) risk = 'Yüksek'; // 54 km/s'den fazla rüzgar
        if (data.main.temp > 30 && data.main.humidity < 40 && data.wind.speed > 10) risk = 'Çok Yüksek';
        
        if (risk === 'Orta') riskColor = 'text-yellow-400';
        if (risk === 'Yüksek') riskColor = 'text-orange-400';
        if (risk === 'Çok Yüksek') riskColor = 'text-red-500';

        ui.fireRisk.textContent = risk;
        ui.fireRisk.className = `text-xl font-bold ${riskColor}`;
    }
    
    // --- MEVCUT FONKSİYONLAR ---
    document.getElementById('get-location-btn').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Tarayıcınız konum servisini desteklemiyor.');
            return;
        }
        document.getElementById('get-location-btn').innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>Konum Aranıyor...`;
        document.getElementById('get-location-btn').disabled = true;
        navigator.geolocation.getCurrentPosition(onLocationSuccess, onLocationError);
    });

    async function onLocationSuccess(position) {
        document.getElementById('location-prompt').classList.add('hidden');
        document.getElementById('main-content').classList.remove('hidden');

        const userLat = position.coords.latitude;
        const userLng = position.coords.longitude;
        
        const map = L.map('map').setView([userLat, userLng], 11);
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
        
        L.circle([userLat, userLng], { radius: 200, color: '#3b82f6', fillColor: '#60a5fa', fillOpacity: 0.5 }).addTo(map).bindPopup('Buradasınız');

        getWeather(userLat, userLng);
        findNearbyIncidents(userLat, userLng, map);
    }

    function onLocationError(error) {
        const prompt = document.getElementById('location-prompt');
        prompt.querySelector('h2').textContent = 'Konum Alınamadı';
        prompt.querySelector('p').textContent = `Konumunuza erişim izni vermediğiniz için yakındaki olaylar gösterilemiyor. Hata: ${error.message}`;
        prompt.querySelector('button').classList.add('hidden');
    }

    async function findNearbyIncidents(userLat, userLng, map) {
    try {
        // ESKİ HALİ: const response = await fetch("{{ asset('data/active-incidents.geojson') }}");
        // YENİ HALİ:
        const response = await fetch("{{ url('/api/incidents') }}");

        const incidents = await response.json();
        const userLatLng = L.latLng(userLat, userLng);

        const nearby = incidents.features.filter(incident => {
            // ... (geri kalan kod aynı)
        });

        // ... (geri kalan kod aynı)

    } catch (e) {
        console.error("Olay verisi yüklenemedi:", e);
    }
}
    </script>
@endpush