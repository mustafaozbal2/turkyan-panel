<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'content' => 'Tarım ve Orman Bakanlığı, ülke genelinde orman yangınlarına daha etkin müdahale edebilmek amacıyla gönüllü itfaiyecilik eğitim programları başlatıyor. Eğitimlere katılmak için başvuru detayları yakında açıklanacak.',
                'image_url' => '/images/news/egitim.jpg', // DEĞİŞTİ
                'published_at' => now()->subDays(1),
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'title' => 'Manavgat Küllerinden Doğuyor: 1 Milyon Fidan Toprakla Buluştu',
                'slug' => 'manavgat-kullerinden-doguyor',
                'content' => 'Geçtiğimiz yıllarda yaşanan büyük Manavgat yangınının ardından başlatılan ağaçlandırma seferberliği kapsamında, gönüllülerin de katılımıyla 1 milyonuncu fidan toprakla buluştu. Bölgenin yeniden yeşermesi için çalışmalar aralıksız devam ediyor.',
                'image_url' => '/images/news/fidan.jpg', // DEĞİŞTİ
                'published_at' => now()->subDays(3),
                'created_at' => now(), 'updated_at' => now()
            ],
        ]);
    }
}