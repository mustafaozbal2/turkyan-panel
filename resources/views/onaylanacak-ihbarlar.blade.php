@extends('layouts.app') {{-- Ana şablonunuzu kullanır --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-6">Onay Bekleyen Gönüllü İhbarları</h1>

    {{-- Başarı veya uyarı mesajları için alan --}}
    @if (session('success'))
        <div class="bg-green-500/20 text-green-300 p-4 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="bg-yellow-500/20 text-yellow-300 p-4 rounded-lg mb-4">
            {{ session('warning') }}
        </div>
    @endif

    <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            @if($reports->isEmpty())
                <div class="p-6 text-center text-gray-400">
                    <i class="fas fa-check-circle text-4xl mb-3"></i>
                    <p class="text-lg">Yakınınızda onay bekleyen gönüllü ihbarı bulunmamaktadır.</p>
                </div>
            @else
                <table class="min-w-full text-sm text-left text-gray-300">
                    <thead class="bg-gray-700/50 text-xs text-gray-400 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Gönüllü</th>
                            <th scope="col" class="px-6 py-3">Tarih</th>
                            <th scope="col" class="px-6 py-3">Konum</th>
                            <th scope="col" class="px-6 py-3">Kanıt</th>
                            <th scope="col" class="px-6 py-3">Açıklama</th>
                            <th scope="col" class="px-6 py-3 text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                            <td class="px-6 py-4 font-medium whitespace-nowrap">
                                {{ $report->user->name ?? 'Bilinmeyen' }}
                                <span class="text-xs text-gray-400 block">Güven: {{ $report->user->trust_score ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}" target="_blank" class="text-blue-400 hover:underline">
                                    Haritada Gör
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $report->image_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $report->image_path) }}" alt="Kanıt" class="w-16 h-16 object-cover rounded-lg hover:opacity-80 transition-opacity">
                                </a>
                            </td>
                            <td class="px-6 py-4 max-w-xs">{{ $report->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center items-center space-x-2">
                                    <form action="{{ route('reports.approve', $report->id) }}" method="POST" onsubmit="return confirm('Bu ihbarı onaylamak istediğinizden emin misiniz?');">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Onayla</button>
                                    </form>
                                    <form action="{{ route('reports.reject', $report->id) }}" method="POST" onsubmit="return confirm('Bu ihbarı reddetmek istediğinizden emin misiniz?');">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">Reddet</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection