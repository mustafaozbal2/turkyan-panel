<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectValueSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('project_values')->insert([
            ['icon' => 'fa-bolt', 'title' => 'Hız', 'description' => 'Tehlike anında saniyelerin önemi vardır. Sistemimiz, en hızlı tespiti ve bildirimi sağlamak üzere tasarlanmıştır.'],
            ['icon' => 'fa-shield-alt', 'title' => 'Güvenilirlik', 'description' => '7/24 kesintisiz çalışan, kararlı ve güvenilir altyapımızla her an göreve hazırız.'],
            ['icon' => 'fa-lightbulb', 'title' => 'İnovasyon', 'description' => 'En son yapay zeka ve sensör teknolojilerini kullanarak sürekli daha iyisi için çalışıyoruz.'],
        ]);
    }
}