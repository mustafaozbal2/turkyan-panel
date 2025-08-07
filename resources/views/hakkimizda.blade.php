@extends('layouts.app')

@section('title', 'Hakkımızda')

@section('content')

<div class="container mx-auto px-4 py-12">

    <section class="text-center mb-20">
        <h1 class="text-5xl font-bold text-white leading-tight">
            Teknolojiyle Yeşeren Umut: <span class="text-orange-500">Türkyan'ın Hikayesi</span>
        </h1>
        <p class="text-gray-400 mt-6 max-w-3xl mx-auto text-lg">
            Biz, doğanın yıkıcı gücüne karşı teknolojinin hassas zekasını, insanlığın ortak vicdanıyla birleştiren bir ekibiz. Yeşil vatanımızı korumak, geleceğe nefes olmak ve her bir canlının yaşam hakkını savunmak için buradayız.
        </p>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-20">
        <div class="bg-gray-800/50 backdrop-blur-sm p-8 rounded-xl shadow-lg border border-gray-700">
            <div class="flex items-center text-orange-500 mb-4">
                <i class="fas fa-rocket text-3xl mr-4"></i>
                <h2 class="text-3xl font-bold text-white">Misyonumuz</h2>
            </div>
            <p class="text-gray-400">
                Yapay zeka, otonom sistemler ve anlık veri analizi gibi en ileri teknolojileri kullanarak orman yangınlarını daha başlamadan öngörmek, başladığı anda en hızlı ve en etkin şekilde müdahale edilmesini sağlamak ve bu süreçte insan faktörünü en aza indirerek can kayıplarını önlemektir.
            </p>
        </div>
        <div class="bg-gray-800/50 backdrop-blur-sm p-8 rounded-xl shadow-lg border border-gray-700">
            <div class="flex items-center text-orange-500 mb-4">
                <i class="fas fa-eye text-3xl mr-4"></i>
                <h2 class="text-3xl font-bold text-white">Vizyonumuz</h2>
            </div>
            <p class="text-gray-400">
                Yangınların sadece bir haber başlığı olduğu, yeşil vatanımızın her karışının teknolojik bir kalkanla korunduğu, insan ve doğanın uyum içinde yaşadığı bir Türkiye hayal ediyoruz. Türkyan'ın, bu alanda dünyaya örnek olan bir ulusal savunma markası olması en büyük hedefimizdir.
            </p>
        </div>
    </section>

    <section class="mb-20">
        <h2 class="text-4xl font-bold text-white text-center mb-10">Temel Değerlerimiz</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(isset($projectValues) && $projectValues->isNotEmpty())
                @foreach($projectValues as $value)
                <div class="bg-gray-800/50 backdrop-blur-sm p-8 rounded-xl text-center shadow-lg border border-gray-700 hover:bg-gray-700 hover:border-orange-500 transition-all duration-300">
                    <i class="{{ $value->icon }} text-orange-500 text-5xl mb-5"></i>
                    <h3 class="text-2xl font-bold text-white mb-2">{{ $value->title }}</h3>
                    <p class="text-gray-400">{{ $value->description }}</p>
                </div>
                @endforeach
            @else
                <p class="text-center text-gray-500 col-span-3">Proje değerleri yüklenemedi.</p>
            @endif
        </div>
    </section>

    <section>
        <h2 class="text-4xl font-bold text-white text-center mb-10">Bu Vizyonu Hayata Geçirenler</h2>
        <div class="flex justify-center flex-wrap gap-8">
             @if(isset($teamMembers) && $teamMembers->isNotEmpty())
                @foreach($teamMembers as $member)
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl text-center p-6 w-72 shadow-lg border border-gray-700">
                    <img src="{{ $member->image_url }}" alt="{{ $member->name }}" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-gray-700">
                    <h3 class="text-xl font-bold text-white">{{ $member->name }}</h3>
                    <p class="text-orange-500 mb-4">{{ $member->role }}</p>
                    <div class="flex justify-center space-x-4 text-gray-400 text-2xl">
                        <a href="{{ $member->linkedin_url }}" target="_blank" class="hover:text-white transition-colors"><i class="fab fa-linkedin"></i></a>
                        <a href="{{ $member->github_url }}" target="_blank" class="hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                @endforeach
            @else
                 <p class="text-center text-gray-500">Ekip üyeleri yüklenemedi.</p>
            @endif
        </div>
    </section>

</div>

@endsection