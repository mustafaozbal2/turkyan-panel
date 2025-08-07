@extends('layouts.app') {{-- Projenizin ana layout dosyasını kullandığınızı varsayıyorum --}}

@section('title', 'Uyarılar')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h1 class="text-4xl font-bold text-white mb-4 sm:mb-0">
            <i class="fas fa-bell text-orange-500"></i>
            Sistem Uyarıları
        </h1>

        {{-- Konum Filtreleme Butonu ve Bilgisi --}}
        <div class="flex items-center space-x-4">
            @if($filteredLocation)
                <div class="text-gray-300">
                    <span class="font-semibold">Filtre:</span>
                    <span class="bg-gray-700 text-orange-400 px-3 py-1 rounded-full">{{ $filteredLocation }}</span>
                    <a href="{{ route('uyarilar') }}" class="ml-2 text-red-500 hover:text-red-400" title="Filtreyi Kaldır">&times;</a>
                </div>
            @endif
            <button id="filterByLocationBtn" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-md transition duration-300 flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>
                <span id="btnText">Konumuma Göre Filtrele</span>
            </button>
        </div>
    </div>

    {{-- Uyarıların Listelendiği Alan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($alerts as $alert)
            <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 shadow-lg transform hover:scale-105 transition-transform duration-300">
                <div class="flex items-center mb-4">
                    <i class="fas {{ $alert->icon }} text-3xl mr-4
                        @switch($alert->severity)
                            @case('Kritik') text-red-500 @break
                            @case('Yüksek') text-orange-500 @break
                            @case('Orta') text-yellow-500 @break
                            @default text-blue-500 @break
                        @endswitch
                    "></i>
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $alert->title }}</h2>
                        <p class="text-sm text-gray-400">{{ $alert->location }}</p>
                    </div>
                </div>
                <p class="text-gray-300 mb-4">{{ $alert->desc }}</p>
                <div class="text-right text-xs text-gray-500">
                    {{ \Carbon\Carbon::parse($alert->created_at)->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-gray-800 rounded-lg">
                <i class="fas fa-check-circle text-5xl text-green-500 mb-4"></i>
                <p class="text-2xl text-gray-300">
                    @if($filteredLocation)
                        <span class="font-bold">{{ $filteredLocation }}</span> konumu için aktif bir uyarı bulunmamaktadır.
                    @else
                        Sistemde aktif bir uyarı bulunmamaktadır.
                    @endif
                </p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('filterByLocationBtn').addEventListener('click', function() {
    const btn = this;
    const btnText = document.getElementById('btnText');
    btnText.textContent = 'Konum Alınıyor...';
    btn.disabled = true;

    if (!navigator.geolocation) {
        alert('Tarayıcınız konum servisini desteklemiyor.');
        btnText.textContent = 'Konumuma Göre Filtrele';
        btn.disabled = false;
        return;
    }

    navigator.geolocation.getCurrentPosition(success, error);

    function success(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;
        const apiKey = '{{ $openWeatherApiKey }}'; // API anahtarını Controller'dan alıyoruz

        // Not: Tarayıcıda API anahtarı kullanmak en güvenli yöntem değildir.
        // Canlı bir projede bu işlemi yapan bir backend rotası oluşturmak daha doğrudur.
        const apiUrl = `https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=${apiKey}&units=metric&lang=tr`;

        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.name) {
                    const city = data.name;
                    // Sayfayı bulunan şehir ile yeniden yükle
                    window.location.href = `{{ route('uyarilar') }}?location=${city}`;
                } else {
                    alert('Konum bilgisi alınamadı. Lütfen tekrar deneyin.');
                    btnText.textContent = 'Konumuma Göre Filtrele';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error('Hata:', err);
                alert('Konum bilgisi alınırken bir hata oluştu.');
                btnText.textContent = 'Konumuma Göre Filtrele';
                btn.disabled = false;
            });
    }

    function error() {
        alert('Konum bilgisi alınamadı. Lütfen tarayıcınızdan konum izni verdiğinizden emin olun.');
        btnText.textContent = 'Konumuma Göre Filtrele';
        btn.disabled = false;
    }
});
</script>
@endpush
