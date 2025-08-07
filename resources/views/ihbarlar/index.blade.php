@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">

    <h1 class="text-4xl font-bold text-cyan-400 mb-6 flex items-center">
        <i class="fas fa-fire-alt mr-3"></i> 
        En Yakın Gönüllü İhbarları ({{ isset($pendingVolunteerReports) ? $pendingVolunteerReports->count() : 0 }})
    </h1>

    {{-- Eğer kullanıcının konumu yoksa --}}
    @if(Auth::user()->latitude === null || Auth::user()->longitude === null)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
            <p><strong>Uyarı:</strong> Konum bilginiz tanımlı değil. Lütfen yönetici ile iletişime geçin.</p>
        </div>
    @endif

    @if(isset($pendingVolunteerReports) && $pendingVolunteerReports->isEmpty())
        <div class="dashboard-panel rounded-xl p-8 text-center text-gray-400">
            <i class="fas fa-check-circle text-4xl mb-3 text-green-500"></i>
            <p class="text-xl">Yakınınızda onay bekleyen gönüllü ihbarı bulunmamaktadır.</p>
        </div>
    @elseif(isset($pendingVolunteerReports))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($pendingVolunteerReports as $report)
                <div class="dashboard-panel pending-panel rounded-xl overflow-hidden flex flex-col shadow-md bg-white">
                    <div class="p-4">
                        <p><strong>Oluşturan:</strong> {{ $report->user->name ?? 'Bilinmiyor' }}</p>
                        <p><strong>Konum:</strong> {{ $report->location ?? '-' }}</p>
                        <p><strong>Tarih:</strong> {{ $report->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Durum:</strong> {{ $report->status }}</p>
                        @if(isset($report->distance))
                            <p><strong>Mesafe:</strong> {{ number_format($report->distance, 2) }} km</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-red-500">
            <strong>Hata:</strong> Gönüllü ihbar verisi yüklenemedi.
        </div>
    @endif

</div>
@endsection
