<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,bakanlik')->only(['create', 'store']);
    }

    public function index()
    {
        $articles = NewsArticle::whereNotNull('published_at')->latest('published_at')->paginate(9);
        return view('news.index', ['articles' => $articles]);
    }

    public function show($slug)
    {
        $article = NewsArticle::where('slug', $slug)->firstOrFail();
        return view('news.show', ['article' => $article]);
    }

    public function create()
    {
        return view('bakanlik.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // --- NİHAİ DÜZELTME: Resim kaydetme mantığı ---
        $imageFile = $request->file('image');
        // Benzersiz bir isim oluştur (örn: 1669814523_manavgat-yangini.jpg)
        $imageName = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $imageFile->getClientOriginalExtension();
        
        // Dosyayı doğrudan public/images/news klasörüne taşı
        $imageFile->move(public_path('images/news'), $imageName);

        // Veritabanına kaydedilecek yolu oluştur
        $imageUrl = 'images/news/' . $imageName;

        NewsArticle::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'content' => $validated['content'],
            'image_url' => $imageUrl, // Veritabanına tam yolu kaydet
            'published_at' => now(),
        ]);

        return redirect()->route('news.index')->with('success', 'Haber başarıyla oluşturuldu!');
    }
}