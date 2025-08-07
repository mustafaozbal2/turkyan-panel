<?php

namespace Database\Seeders;

// Bu satırı ve altındakileri elle yazmanıza gerek yok, sadece içeriği tamamen değiştirin.
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // HATA DÜZELTMESİ: EKSİK OLAN SATIR BU

class AlertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('alerts')->insert([
            [
                'severity' => 'Kritik',
                'icon' => 'fa-fire-alt',
                'title' => 'Termal Anomali Saptandı',
                'location' => 'Muğla, Fethiye',
                'desc' => 'Uydu görüntülerinde termal anomali saptandı. Acil kontrol gerekli.',
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
            [
                'severity' => 'Yüksek',
                'icon' => 'fa-wind',
                'title' => 'Kuvvetli Rüzgar Uyarısı',
                'location' => 'İzmir, Çeşme',
                'desc' => 'Beklenen rüzgar hızı 70 km/s üzerine çıkabilir. Yangın yayılma riski arttı.',
                'created_at' => now()->subHour(),
                'updated_at' => now()->subHour(),
            ],
            [
                'severity' => 'Orta',
                'icon' => 'fa-satellite-dish',
                'title' => 'Sensör Bağlantı Sorunu',
                'location' => 'Bolu, Gerede',
                'desc' => 'Gözetleme Kulesi (KULE-07) ile bağlantı 5 dakikadır kesik.',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
        ]);
    }
}