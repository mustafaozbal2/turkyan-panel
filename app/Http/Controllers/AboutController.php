<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMember;      // TeamMember modelini çağırıyoruz
use App\Models\ProjectValue;   // ProjectValue modelini çağırıyoruz

class AboutController extends Controller
{
    /**
     * Hakkımızda sayfasını gösterir ve gerekli verileri gönderir.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Veritabanından tüm ekip üyelerini çek
        $teamMembers = TeamMember::all();

        // Veritabanından tüm proje değerlerini çek
        $projectValues = ProjectValue::all();

        // Çektiğimiz verileri 'hakkimizda' view'ine gönder
        return view('hakkimizda', [
            'teamMembers' => $teamMembers,
            'projectValues' => $projectValues
        ]);
    }
}