<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // HATA DÜZELTMESİ: EKSİK OLAN SATIR BU

class IncidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    DB::table('incidents')->insert([
        ['name' => 'Manavgat Yangını', 'location' => 'Akdeniz', 'severity' => 'Kritik', 'area_hectares' => 150.5, 'response_time_minutes' => 25, 'latitude' => 36.890, 'longitude' => 31.540, 'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(5)],
        ['name' => 'Fethiye Yangını', 'location' => 'Ege', 'severity' => 'Yüksek', 'area_hectares' => 75.0, 'response_time_minutes' => 18, 'latitude' => 36.620, 'longitude' => 29.110, 'created_at' => now()->subDays(10), 'updated_at' => now()->subDays(10)],
        ['name' => 'Datça Yangını', 'location' => 'Ege', 'severity' => 'Yüksek', 'area_hectares' => 90.2, 'response_time_minutes' => 22, 'latitude' => 36.726, 'longitude' => 27.683, 'created_at' => now()->subDays(12), 'updated_at' => now()->subDays(12)],
        ['name' => 'Kastamonu Yangını', 'location' => 'Karadeniz', 'severity' => 'Orta', 'area_hectares' => 20.0, 'response_time_minutes' => 35, 'latitude' => 41.376, 'longitude' => 33.775, 'created_at' => now()->subDays(20), 'updated_at' => now()->subDays(20)],
        ['name' => 'Çeşme Yangını', 'location' => 'Ege', 'severity' => 'Kritik', 'area_hectares' => 250.0, 'response_time_minutes' => 15, 'latitude' => 38.324, 'longitude' => 26.305, 'created_at' => now()->subDays(2), 'updated_at' => now()->subDays(2)],
    ]);
}
}