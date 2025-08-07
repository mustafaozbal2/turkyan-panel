@extends('layouts.app')

@section('title', 'Komuta Kontrol Merkezi')

@push('styles')
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .dashboard-panel { background-color: rgba(28, 28, 32, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .panel-header { position: relative; padding-bottom: 0.75rem; }
        .panel-header::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background-color: #F97316; border-radius: 2px; }
        #map, #camera-feed-container { border-radius: 0.5rem; background-color: #1a1a1a; }
        .camera-feedback { color: #9CA3AF; }
        .pending-panel {
            border: 1px solid #f59e0b;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0% { box-shadow: 0 0 15px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 0 25px rgba(245, 158, 11, 0.7); }
            100% { box-shadow: 0 0 15px rgba(245, 158, 11, 0.3); }
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 text-white">

    <!-- BİLDİRİM ALANI -->
    @if (session('success'))
        <div class="bg-green-500/90 border border-green-400 text-white px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Başarılı!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if (session('info'))
        <div class="bg-blue-500/90 border border-blue-400 text-white px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Bilgi:</strong>
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    @endif

    <!-- ONAY BEKLEYEN TESPİTLER BÖLÜMÜ -->
    @if($pendingIncidents->isNotEmpty())
    @if($pendingVolunteerReports->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-2xl font-bold text-cyan-400 mb-4 flex items-center">
            <i class="fas fa-users fa-beat mr-3"></i> GÖNÜLLÜ İHBARLARI ({{ $pendingVolunteerReports->count() }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendingVolunteerReports as $report)
                <div class="dashboard-panel pending-panel border-cyan-500 rounded-xl overflow-hidden">
                    <a href="{{ asset('storage/' . $report->image_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $report->image_path) }}" alt="Gönüllü Kanıt Fotoğrafı" class="w-full h-48 object-cover cursor-pointer">
                    </a>
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg">Gönüllü Raporu</h3>
                                <p class="text-xs text-gray-400">Gönderen: {{ $report->user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Güven Puanı</p>
                                <p class="font-bold text-lg {{ $report->user->trust_score >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $report->user->trust_score }}</p>
                            </div>
                        </div>

                        @if($report->description)
                        <p class="text-sm text-gray-300 mt-2 bg-gray-900/50 p-2 rounded-md">
                            <i class="fas fa-comment-dots mr-1 text-cyan-400"></i> {{ $report->description }}
                        </p>
                        @endif
@if($pendingVolunteerReports->isNotEmpty())
    <div class="mb-10">
        <h2 class="text-2xl font-bold text-cyan-400 mb-4 flex items-center">
            <i class="fas fa-users fa-beat mr-3"></i> GÖNÜLLÜ İHBARLARI ({{ $pendingVolunteerReports->count() }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pendingVolunteerReports as $report)
                <div class="dashboard-panel pending-panel border-cyan-500 rounded-xl overflow-hidden">
                    <a href="{{ asset('storage/' . $report->image_path) }}" target="_blank">
                        <img src="{{ asset('storage/' . $report->image_path) }}" alt="Gönüllü Kanıt Fotoğrafı" class="w-full h-48 object-cover cursor-pointer">
                    </a>
                    <div class="p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg">Gönüllü Raporu</h3>
                                <p class="text-xs text-gray-400">Gönderen: {{ $report->user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">Güven Puanı</p>
                                <p class="font-bold text-lg {{ $report->user->trust_score >= 0 ? 'text-green-400' : 'text-red-400' }}">{{ $report->user->trust_score }}</p>
                            </div>
                        </div>

                        @if($report->description)
                        <p class="text-sm text-gray-300 mt-2 bg-gray-900/50 p-2 rounded-md">
                            <i class="fas fa-comment-dots mr-1 text-cyan-400"></i> {{ $report->description }}
                        </p>
                        @endif

                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-map-marker-alt mr-1"></i> {{ $report->latitude }}, {{ $report->longitude }}</p>

                        <form action="{{ route('volunteer.report.handle', $report) }}" method="POST" class="mt-4 grid grid-cols-2 gap-3">
                            @csrf
                            <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-md transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i> Onayla
                            </button>
                            <button type="submit" name="action" value="reject" class="bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition-all duration-200 flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i> Reddet
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <hr class="border-gray-700 my-8">
@endif

<!-- ONAY BEKLEYEN AI TESPİTLERİ BÖLÜMÜ -->
@if($pendingIncidents->isNotEmpty())
    <!-- ... Bu bölüm aynı kalıyor ... -->
@endif
                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-map-marker-alt mr-1"></i> {{ $report->latitude }}, {{ $report->longitude }}</p>

                       <form action="{{ route('volunteer.report.handle', $report) }}" method="POST" class="mt-4 grid grid-cols-2 gap-3">
    @csrf
    <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-500 ...">
        <i class="fas fa-check mr-2"></i> Onayla
    </button>
    <button type="submit" name="action" value="reject" class="bg-red-700 hover:bg-red-600 ...">
        <i class="fas fa-times mr-2"></i> Reddet
    </button>
</form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <hr class="border-gray-700 my-8">
@endif
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-yellow-400 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle fa-beat mr-3"></i> ONAY BEKLEYEN TESPİTLER ({{ $pendingIncidents->count() }})
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pendingIncidents as $incident)
                    <div class="dashboard-panel pending-panel rounded-xl overflow-hidden">
                        <a href="{{ $incident->evidence_image_url }}" target="_blank">
                            <img src="{{ $incident->evidence_image_url }}" alt="Kanıt Fotoğrafı" class="w-full h-48 object-cover cursor-pointer">
                        </a>
                        <div class="p-4">
                            <h3 class="font-bold text-lg">AI Tespit: {{ $incident->created_at->diffForHumans() }}</h3>
                            <div class="text-sm text-gray-300 mt-2 space-y-1">
                                <p><i class="fas fa-map-marker-alt w-4 text-center mr-1 text-red-400"></i> {{ $incident->latitude }}, {{ $incident->longitude }}</p>
                                <p><i class="fas fa-brain w-4 text-center mr-1 text-purple-400"></i> Güven Skoru: <span class="font-semibold">{{ $incident->confidence_score * 100 }}%</span></p>
                                <p><i class="fas fa-expand-arrows-alt w-4 text-center mr-1 text-orange-400"></i> Tahmini Büyüklük: <span class="font-semibold capitalize">{{ $incident->estimated_size }}</span></p>
                            </div>
                            <form action="{{ route('incidents.handle', $incident) }}" method="POST" class="mt-4 grid grid-cols-2 gap-3">
                                @csrf
                                <button type="submit" name="action" value="approve" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-md transition-all duration-200 flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i> Onayla
                                </button>
                                <button type="submit" name="action" value="reject" class="bg-red-700 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition-all duration-200 flex items-center justify-center">
                                    <i class="fas fa-times mr-2"></i> Reddet
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <hr class="border-gray-700 my-8">
    @endif

    <!-- ANA KOMUTA MERKEZİ -->
    <h1 class="text-4xl font-bold mb-6">Komuta Kontrol Merkezi</h1>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sol Sütun: Olaylar ve Gözetleme -->
        <div class="lg:col-span-1 flex flex-col gap-8">
            <div class="dashboard-panel rounded-xl p-6 flex-grow flex flex-col">
                <h2 class="panel-header text-xl font-bold text-white">Aktif Olaylar</h2>
                <div class="mt-4 space-y-3 flex-grow overflow-y-auto max-h-[40vh]">
                    @forelse($activeIncidents as $incident)
                        <div id="incident-card-{{ $incident->id }}" class="bg-gray-900/50 p-3 rounded-md cursor-pointer hover:bg-orange-600/20 transition-colors" onclick="flyToIncident([{{ $incident->latitude }}, {{ $incident->longitude }}])">
                            <div class="flex justify-between items-center">
                                <p class="font-semibold">{{ $incident->name }}</p>
                                <p class="text-sm text-orange-400">{{ $incident->severity }}</p>
                            </div>
                            <div id="drone-status-container-{{ $incident->id }}" class="text-xs text-cyan-400 mt-1 animate-pulse {{ $incident->drone_status ? '' : 'hidden' }}">
                                <i class="fas fa-satellite-dish mr-1"></i> Drone Durumu: <span id="drone-status-text-{{ $incident->id }}">{{ $incident->drone_status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400">İzlenen aktif bir olay bulunmamaktadır.</p>
                    @endforelse
                </div>
            </div>
             <div class="dashboard-panel rounded-xl p-6 flex flex-col">
                <h2 class="panel-header text-xl font-bold text-white">Gözetleme Görüntüsü</h2>
                <div id="camera-feed-container" class="mt-4 flex-grow bg-black flex items-center justify-center">
                    <video id="camera-feed" class="w-full h-full object-cover hidden" autoplay muted playsinline></video>
                    <div id="camera-feedback" class="camera-feedback text-center p-4">
                        <i class="fas fa-video-slash text-4xl mb-2"></i>
                        <p>Kamera erişimi bekleniyor...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orta ve Sağ Sütun: Harita ve Analiz -->
        <div class="lg:col-span-2 flex flex-col gap-8">
            <div class="dashboard-panel rounded-xl p-6 flex-grow flex flex-col h-[85vh]">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                    <div class="md:col-span-2">
                         <h2 class="panel-header text-xl font-bold text-white">Operasyon Haritası</h2>
                    </div>
                    <div class="md:col-span-1 dashboard-panel border-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-bold text-white text-center mb-2">Anlık Analiz (<span id="weather-location">--</span>)</h3>
                        <div id="weather-loader" class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm z-20 hidden items-center justify-center rounded-xl"><i class="fas fa-spinner fa-spin text-3xl text-white"></i></div>
                        <div id="weather-content" class="text-xs">
                            <p class="text-gray-400 text-center">Analiz için haritadan bir nokta seçin.</p>
                        </div>
                    </div>
                </div>
                <div id="map" class="w-full h-full mt-2 bg-gray-700 flex-grow"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- DEĞİŞKENLER ---
        const incidentsForMap = @json($incidentsForMap);
        const activeIncidents = @json($activeIncidents);
        const OWM_API_KEY = 'c7f5d2cf86c25eb85b206e27995e181c';
        let map;
        let clickMarker;

        // --- FONKSİYONLAR (TAM VE EKSİKSİZ HALİ) ---

        function initializeMap() {
            map = L.map('map').setView([39.0, 35.0], 6);
            L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
            }).addTo(map);

            incidentsForMap.forEach(incident => {
                const fireIcon = L.divIcon({
                    html: `<i class="fas fa-fire-alt text-2xl" style="color:${getSeverityColor(incident.status)}; text-shadow:0 0 8px #000;"></i>`,
                    className: 'border-0 bg-transparent', iconSize: [24, 24]
                });
                const marker = L.marker([incident.lat, incident.lng], { icon: fireIcon }).addTo(map);
                marker.bindPopup(`<b>${incident.name}</b><br>${incident.area}`);
            });

            clickMarker = L.marker([0, 0], { opacity: 0 });
            
            map.on('click', function(e) {
                const { lat, lng } = e.latlng;
                clickMarker.setLatLng(e.latlng).setOpacity(1).addTo(map);
                getWeather(lat, lng);
            });

            if (incidentsForMap.length > 0) {
                map.setView([incidentsForMap[0].lat, incidentsForMap[0].lng], 10);
            }
        }
        
        function startCamera() {
            const videoElement = document.getElementById('camera-feed');
            const feedbackElement = document.getElementById('camera-feedback');
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => {
                        videoElement.srcObject = stream;
                        videoElement.play();
                        videoElement.classList.remove('hidden');
                        feedbackElement.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error("Kamera hatası: ", error);
                        feedbackElement.innerHTML = `<i class="fas fa-exclamation-triangle text-4xl mb-2 text-red-500"></i><p>Kamera erişimi reddedildi veya bulunamadı.</p>`;
                    });
            } else {
                feedbackElement.innerHTML = `<i class="fas fa-exclamation-circle text-4xl mb-2 text-yellow-500"></i><p>Tarayıcınız kamera erişimini desteklemiyor.</p>`;
            }
        }

        async function getWeather(lat, lon) {
            const loader = document.getElementById('weather-loader');
            const content = document.getElementById('weather-content');
            const locationSpan = document.getElementById('weather-location');
            
            loader.style.display = 'flex';
            locationSpan.textContent = "Yükleniyor...";
            content.innerHTML = '';

            try {
                const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${OWM_API_KEY}&units=metric&lang=tr`);
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                locationSpan.textContent = data.name || "Bilinmeyen";
                
                let risk = 'Düşük';
                if (data.wind.speed > 10) risk = 'Orta';
                if (data.wind.speed > 15) risk = 'Yüksek';
                if (data.main.temp > 30 && data.main.humidity < 40 && data.wind.speed > 10) risk = 'Çok Yüksek';

                content.innerHTML = `
                    <div class="grid grid-cols-2 gap-2">
                        <div><strong>Durum:</strong> <span class="capitalize">${data.weather[0].description}</span></div>
                        <div><strong>Sıcaklık:</strong> ${Math.round(data.main.temp)}°C</div>
                        <div><strong>Nem:</strong> %${data.main.humidity}</div>
                        <div><strong>Rüzgar:</strong> ${(data.wind.speed * 3.6).toFixed(1)} km/s</div>
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-700 text-center">
                        <strong>Yangın Riski:</strong> <span class="font-bold" style="color: ${getSeverityColor(risk)}">${risk}</span>
                    </div>
                `;
            } catch (error) {
                console.error("Hava durumu API hatası:", error);
                locationSpan.textContent = "Hata";
                content.innerHTML = `<p class="text-red-400 text-xs">Veri alınamadı. Detaylar için konsolu (F12) kontrol edin.</p>`;
            } finally {
                loader.style.display = 'none';
            }
        }
        
        function getSeverityColor(severity) {
            switch (severity) {
                case 'Kritik': case 'Çok Yüksek': return '#ef4444';
                case 'Yüksek': return '#f97316';
                case 'Orta': return '#facc15';
                default: return '#34d399';
            }
        }

        window.flyToIncident = function(coords) {
            if (map) {
                map.flyTo(coords, 12);
                clickMarker.setLatLng(coords).setOpacity(1).addTo(map);
                getWeather(coords[0], coords[1]);
            }
        }

        function initializeEchoListeners() {
            if (typeof window.Echo !== 'undefined') {
                console.log("Echo hazır, anlık güncellemeler dinleniyor.");
                
                activeIncidents.forEach(incident => {
                    window.Echo.private(`incident.${incident.id}`)
                        .listen('DroneStatusUpdated', (e) => {
                            console.log(`Olay ${e.incident.id} için yeni drone durumu:`, e.incident.drone_status);
                            const statusContainer = document.getElementById(`drone-status-container-${e.incident.id}`);
                            const statusText = document.getElementById(`drone-status-text-${e.incident.id}`);
                            if(statusContainer && statusText) {
                                statusText.textContent = e.incident.drone_status;
                                statusContainer.classList.remove('hidden');
                            }
                        });
                });

            } else {
                console.log("Echo henüz hazır değil, 100ms sonra tekrar denenecek.");
                setTimeout(initializeEchoListeners, 100);
            }
        }
        
        // --- BAŞLATMA ---
        initializeMap();
        startCamera();
        initializeEchoListeners();
    });
    </script>
@endpush
