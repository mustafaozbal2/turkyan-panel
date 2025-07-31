@extends('layouts.app')
@section('title', 'Raporlar ve Analiz')
@push('styles')
    {{-- ... (style kodları aynı kalıyor) ... --}}
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
        {{-- Başarı oranı şimdilik statik kalabilir veya daha sonra hesaplanabilir --}}
        <div class="dashboard-panel p-6 rounded-xl">
            <div class="flex items-center">
                <div class="bg-green-500/10 p-3 rounded-lg mr-4"><i class="fas fa-check-circle text-2xl text-green-500"></i></div>
                <div>
                    <h3 class="text-gray-400 text-sm font-medium">Başarı Oranı</h3>
                    <p class="text-3xl font-bold text-white mt-1">97%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreler ve Rapor Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="dashboard-panel rounded-xl overflow-hidden flex flex-col p-6">
             <h3 class="text-xl font-bold text-white mb-4">Olayların Önem Seviyesine Göre Dağılımı</h3>
             <div class="h-64 flex items-center justify-center">
                 <canvas id="severityChart"></canvas>
             </div>
        </div>
        <div class="dashboard-panel rounded-xl overflow-hidden flex flex-col p-6">
             <h3 class="text-xl font-bold text-white mb-4">Bölgelere Göre Olay Dağılımı</h3>
             <div class="h-64 flex items-center justify-center">
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
    // Controller'dan gelen verileri al
    const severityData = @json($severityChartData);
    const regionData = @json($regionChartData);

    Chart.defaults.color = '#9CA3AF';
    Chart.defaults.borderColor = '#4B5563';
    const chartColors = [ 'rgba(239, 68, 68, 0.7)', 'rgba(249, 115, 22, 0.7)', 'rgba(234, 179, 8, 0.7)', 'rgba(59, 130, 246, 0.7)'];

    // Önem Seviyesi Grafiği
    const severityCtx = document.getElementById('severityChart');
    if (severityCtx) {
        new Chart(severityCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(severityData),
                datasets: [{ data: Object.values(severityData), backgroundColor: chartColors }]
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
                datasets: [{ data: Object.values(regionData), backgroundColor: chartColors }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }
});
</script>
@endpush