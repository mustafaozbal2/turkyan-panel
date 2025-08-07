<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('news_articles')->insert([
            [
                'title' => 'Gönüllü İtfaiyecilik Eğitimleri Başlıyor',
                'slug' => 'gonullu-itfaiyecilik-egitimleri-basliyor',
                'content' => '...',
                'image_url' => 'images/news/egitim.jpg', // DOĞRU YOL
                // ...
            ],
            [
                'title' => 'Manavgat Küllerinden Doğuyor...',
                'slug' => 'manavgat-kullerinden-doguyor',
                'content' => '...',
                'image_url' => 'images/news/fidan.jpg', // DOĞRU YOL
                // ...
            ],
        ]);
    }
}