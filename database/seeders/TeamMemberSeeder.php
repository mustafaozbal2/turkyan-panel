<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('team_members')->insert([
            ['name' => 'Mustafa Özbal', 'role' => 'Proje Lideri & Geliştirici', 'image_url' => 'https://placehold.co/400x400/27272a/FFFFFF?text=MÖ'],
            ['name' => 'Reyhan Er', 'role' => 'Arayüz Tasarımı & Frontend', 'image_url' => 'https://placehold.co/400x400/27272a/FFFFFF?text=RE'],
            ['name' => 'Bayram Cellat', 'role' => 'Sistem Analisti & Veri Uzmanı', 'image_url' => 'https://placehold.co/400x400/27272a/FFFFFF?text=BC'],
        ]);
    }
}