@extends('layouts.app')
@section('title', 'Yeni Haber Ekle')
@section('content')
<div class="container mx-auto px-4 py-8 text-white">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold">Yeni Haber Oluştur</h1>
            <a href="{{ route('news.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">
                &larr; Haber Listesine Geri Dön
            </a>
        </div>
        <div class="dashboard-panel rounded-xl p-8">
            <form action="{{ route('news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300">Haber Başlığı</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required class="form-input mt-1 block w-full rounded-md p-3 bg-gray-700 border-gray-600 text-white">
                        @error('title') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-300">Haber İçeriği</label>
                        <textarea name="content" id="content" rows="10" required class="form-input mt-1 block w-full rounded-md p-3 bg-gray-700 border-gray-600 text-white">{{ old('content') }}</textarea>
                        @error('content') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-300">Haber Görseli</label>
                        <input type="file" name="image" id="image" required class="form-input mt-1 block w-full text-gray-400 rounded-md p-2 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-500/20 file:text-orange-300 hover:file:bg-orange-600/30">
                        @error('image') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit" class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 px-6 rounded-md transition-colors duration-200">Haberi Yayınla</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection