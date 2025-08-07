@extends('layouts.app')

@section('title', 'Yeni Yangın İhbarı')

@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('dashboard') }}" class="text-orange-400 hover:text-orange-300 mr-4">&larr; Panele Geri Dön</a>
            <h1 class="text-4xl font-bold">Yeni Yangın İhbarı</h1>
        </div>

        <div class="dashboard-panel rounded-xl p-8">
            <form action="{{ route('volunteer.report.store') }}" method="POST" enctype="multipart/form-data" id="report-form">
                @csrf
                <div class="space-y-6">
                    <!-- Fotoğraf Yükleme -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300 mb-2">1. Kanıt Fotoğrafı Yükle</label>
                        <input type="file" name="image" id="image" accept="image/*" capture="environment" required class="form-input block w-full text-gray-400 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-500/20 file:text-orange-300 hover:file:bg-orange-600/30">
                        <p class="text-xs text-gray-500 mt-1">Mobil cihazlarda doğrudan kameranız açılacaktır.</p>
                        @error('image') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Konum Alma -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">2. Konumunu Onayla</label>
                        <button type="button" id="get-location-btn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-md transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-crosshairs mr-2"></i> Konumumu Al
                        </button>
                        <div id="location-feedback" class="text-center text-sm text-yellow-400 mt-2 h-5"></div>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        @error('latitude') <p class="mt-1 text-sm text-red-400">Konum bilgisi alınamadı.</p> @enderror
                    </div>

                    <!-- Açıklama -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300">3. (İsteğe Bağlı) Kısa Açıklama</label>
                        <textarea name="description" id="description" rows="3" class="form-input mt-1 block w-full rounded-md p-3 bg-gray-700 border-gray-600 text-white" placeholder="Örn: Şahinbey Parkı'nın arkasından yoğun duman yükseliyor.">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <!-- Gönder Butonu -->
                    <div class="pt-4">
                        <button type="submit" id="submit-btn" disabled class="w-full bg-gray-600 text-white font-bold py-3 px-4 rounded-md cursor-not-allowed transition-colors duration-200 flex items-center justify-center">
                            <span id="submit-btn-text">Lütfen Konum Bilgisi Alın</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const getLocationBtn = document.getElementById('get-location-btn');
        const locationFeedback = document.getElementById('location-feedback');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const submitBtn = document.getElementById('submit-btn');
        const submitBtnText = document.getElementById('submit-btn-text');

        getLocationBtn.addEventListener('click', () => {
            if (!navigator.geolocation) {
                locationFeedback.textContent = 'Tarayıcınız konum servisini desteklemiyor.';
                return;
            }

            locationFeedback.textContent = 'Konum alınıyor, lütfen bekleyin...';
            getLocationBtn.disabled = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    latitudeInput.value = lat;
                    longitudeInput.value = lon;

                    locationFeedback.textContent = `Konum başarıyla alındı! (${lat.toFixed(4)}, ${lon.toFixed(4)})`;
                    locationFeedback.classList.remove('text-yellow-400');
                    locationFeedback.classList.add('text-green-400');

                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-600', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-red-600', 'hover:bg-red-500');
                    submitBtnText.innerHTML = '<i class="fas fa-paper-plane mr-2"></i> İhbarı Gönder';
                    getLocationBtn.disabled = false;
                },
                () => {
                    locationFeedback.textContent = 'Konum alınamadı. Lütfen tarayıcı izinlerini kontrol edin.';
                    getLocationBtn.disabled = false;
                }
            );
        });
    });
</script>
@endpush
