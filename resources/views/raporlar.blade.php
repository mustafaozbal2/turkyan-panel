@extends('layouts.app')
@section('title', 'Raporlar ve Analiz')
@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .dashboard-panel { background-color: rgba(28, 28, 32, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease; }
        .dashboard-panel:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); border-color: rgba(249, 115, 22, 0.4); }
        .panel-header { position: relative; padding-bottom: 0.75rem; }
        .panel-header::after { content: ''; position: absolute; bottom: 0; left: 0; width: 40px; height: 3px; background-color: #F97316; border-radius: 2px; }
        .custom-input { background-color: rgba(17, 24, 39, 0.8); border: 1px solid #4A5568; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #E5E7EB; transition: border-color 0.2s, box-shadow 0.2s; }
        .custom-input:focus { outline: none; border-color: #F97316; box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.5); }
    </style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="text-left mb-10">
        <h1 class="text-4xl font-bold">Raporlama ve Analiz Merkezi</h1>
        <p class="text-gray-400 mt-2">Sistemin genel performansını ve olay analizlerini buradan inceleyin.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="dashboard-panel p-6 rounded-xl">
            <div class="flex items-center">
                <div class="bg-orange-500/10 p-3 rounded-lg mr-4"><i class="fas fa-fire-extinguisher text-2xl text-orange-500"></i></div>
                <div>
                    <h3 class="text-gray-400 text-sm font-medium">Toplam Olay (Son 30 Gün)</h3>
                    <p class="text-3xl font-bold text-white mt-1">{{ $totalIncidents }}</p>
                </div>
            </div>
        </div>
        <div class="dashboard-panel p-6 rounded-xl">
            <div class="flex items-center">
                <div class="bg-blue-500/10 p-3 rounded-lg mr-4"><i class="fas fa-clock text-2xl text-blue-500"></i></div>
                <div>
                    <h3 class="text-gray-400 text-sm font-medium">Ort. Müdahale Süresi</h3>
                    <p class="text-3xl font-bold text-white mt-1">{{ round($avgResponseTime) }} dk</p>
                </div>
            </div>
        </div>
        <div class="dashboard-panel p-6 rounded-xl">
            <div class="flex items-center">
                <div class="bg-yellow-500/10 p-3 rounded-lg mr-4"><i class="fas fa-map-marker-alt text-2xl text-yellow-500"></i></div>
                <div>
                    <h3 class="text-gray-400 text-sm font-medium">En Aktif Bölge</h3>
                    <p class="text-3xl font-bold text-white mt-1">{{ $mostActiveRegion }}</p>
                </div>
            </div>
        </div>
        <div class="dashboard-panel p-6 rounded-xl">
            <div class="flex items-center">
                <div class="bg-green-500/10 p-3 rounded-lg mr-4"><i class="fas fa-check-circle text-2xl text-green-500"></i></div>
                <div>
                    <h3 class="text-gray-400 text-sm font-medium">Başarı Oranı</h3>
                    <p class="text-3xl font-bold text-white mt-1">97%</p> {{-- Bu şimdilik statik --}}
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="panel-header text-2xl font-semibold mb-4 md:mb-0">Genel Analizler</h2>
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Tarih Aralığı Seç..." class="custom-input w-48">
            <input type="text" placeholder="Rapor ara..." class="custom-input w-48">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="dashboard-panel rounded-xl overflow-hidden flex flex-col p-6">
             <h3 class="text-xl font-bold text-white mb-4">Olayların Önem Seviyesine Göre Dağılımı</h3>
             <div class="h-64 flex-grow flex items-center justify-center">
                 <canvas id="severityChart"></canvas>
             </div>
        </div>
        <div class="dashboard-panel rounded-xl overflow-hidden flex flex-col p-6">
             <h3 class="text-xl font-bold text-white mb-4">Bölgelere Göre Olay Dağılımı</h3>
             <div class="h-64 flex-grow flex items-center justify-center">
                 <canvas id="regionChart"></canvas>
             </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Controller'dan gelen dinamik verileri al
    const severityData = @json($severityChartData);
    const regionData = @json($regionChartData);

    Chart.defaults.color = '#9CA3AF';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';
    const chartColors = [ 'rgba(239, 68, 68, 0.8)', 'rgba(249, 115, 22, 0.8)', 'rgba(234, 179, 8, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(34, 197, 94, 0.8)'];

    // Önem Seviyesi Grafiği
    const severityCtx = document.getElementById('severityChart');
    if (severityCtx) {
        new Chart(severityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(severityData),
                datasets: [{ data: Object.values(severityData), backgroundColor: chartColors, borderColor: 'rgba(28, 28, 32, 0.7)', borderWidth: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // Bölgelere Göre Dağılım Grafiği
    const regionCtx = document.getElementById('regionChart');
    if (regionCtx) {
        new Chart(regionCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(regionData),
                datasets: [{ data: Object.values(regionData), backgroundColor: chartColors, borderColor: 'rgba(28, 28, 32, 0.7)', borderWidth: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>
@endpush