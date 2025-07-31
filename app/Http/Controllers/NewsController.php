<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    // Haber listesi sayfası
    public function index()
    {
        $articles = NewsArticle::whereNotNull('published_at')
                                ->latest('published_at')
                                ->paginate(10); // Sayfalama

        return view('news.index', ['articles' => $articles]);
    }

    // Tekil haber detay sayfası
    public function show($slug)
    {
        $article = NewsArticle::where('slug', $slug)->firstOrFail();
        return view('news.show', ['article' => $article]);
    }
}