@extends('layouts.app')

@section('title', 'Yangın Bilgilendirme')

@section('content')
<div class="container mx-auto px-4 py-10 text-white">

    <!-- Başlık -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-orange-500 mb-2">Yangın Anında Ne Yapmalıyım?</h1>
        <p class="text-gray-300 text-lg max-w-2xl mx-auto">
            Aşağıdaki görsel rehberle yangın anında doğru adımları öğrenin. PDF'yi indirerek çevrimdışı da erişebilirsiniz.
        </p>
    </div>

    <!-- Görsellerle Bilgilendirme Kartları -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto mb-12">
        <!-- Görsel 1 -->
        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-md">
            <img src="{{ asset('images/bilgi1.png') }}" alt="Yangını fark et" class="w-full h-64 object-cover">
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2 text-orange-400">Yangını Fark Et</h3>
                <p class="text-gray-300">Duman, ısı veya alev gördüğünüzde hızlıca çıkış yollarını değerlendirin.</p>
            </div>
        </div>

        <!-- Görsel 2 -->
        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-md">
            <img src="{{ asset('images/bilgi2.png') }}" alt="İtfaiyeyi Ara" class="w-full h-64 object-cover">
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2 text-orange-400">İtfaiyeye Haber Ver</h3>
                <p class="text-gray-300">112 numarasını arayarak açık ve net şekilde konumu bildir.</p>
            </div>
        </div>

        <!-- Görsel 3 -->
        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-md">
            <img src="{{ asset('images/bilgi3.png') }}" alt="Tahliye Ol" class="w-full h-64 object-cover">
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2 text-orange-400">Güvenli Tahliye</h3>
                <p class="text-gray-300">Asansörleri kullanmadan en yakın çıkıştan hızlıca uzaklaş.</p>
            </div>
        </div>

        <!-- Görsel 4 -->
        <div class="bg-gray-800 rounded-xl overflow-hidden shadow-md">
            <img src="{{ asset('images/bilgi4.png') }}" alt="Toplanma Alanı" class="w-full h-64 object-cover">
            <div class="p-4">
                <h3 class="text-xl font-semibold mb-2 text-orange-400">Toplanma Noktasına Git</h3>
                <p class="text-gray-300">Belirlenen alanda toplanarak sayım yapılmasını bekle.</p>
            </div>
        </div>
    </div>

    <!-- PDF İndir -->
    <div class="text-center mb-12">
        <a href="{{ asset('pdf/yangin-bilgilendirme.pdf') }}" download
            class="inline-flex items-center bg-orange-600 hover:bg-orange-500 text-white font-semibold py-2 px-6 rounded-lg transition">
            <i class="fas fa-file-download mr-2"></i> PDF Olarak İndir
        </a>
    </div>

    <!-- Acil Destek Numaraları -->
    <div class="bg-gray-900 rounded-xl shadow-lg p-6 max-w-4xl mx-auto">
        <h2 class="text-2xl font-semibold text-white mb-4">Acil Destek Numaraları</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300">
            <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-lg">
                <i class="fas fa-phone text-orange-500 text-xl"></i>
                <div>
                    <p class="text-sm">Yangın İhbar</p>
                    <p class="font-semibold">112</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-lg">
                <i class="fas fa-hospital text-orange-500 text-xl"></i>
                <div>
                    <p class="text-sm">Ambulans</p>
                    <p class="font-semibold">112</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 bg-gray-800 p-4 rounded-lg">
                <i class="fas fa-user-shield text-orange-500 text-xl"></i>
                <div>
                    <p class="text-sm">Afet Acil</p>
                    <p class="font-semibold">122</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
