<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request; // Request sınıfını kullanacağımızı belirtiyoruz

class AlertController extends Controller
{
    /**
     * Uyarıları listeler.
     * Eğer bir konum belirtilmişse, sadece o konuma ait uyarıları listeler.
     */
    public function index(Request $request)
    {
        // Temel sorguyu oluşturalım
        $query = Alert::query();

        // URL'den 'location' adında bir parametre gelip gelmediğini kontrol et
        if ($request->has('location') && $request->location != '') {
            $location = $request->location;
            // Veritabanında 'location' sütununda gelen şehir adını içeren kayıtları bul
            // Örn: "Gaziantep" araması, "Gaziantep İtfaiyesi - Şehitkamil" kaydını bulacaktır.
            $query->where('location', 'LIKE', '%' . $location . '%');
        } else {
            $location = null; // Filtre yok
        }

        // Sorguyu en yeniden eskiye doğru sırala ve sonuçları al
        $alerts = $query->latest()->get();

        // View'a hem filtrelenmiş uyarıları hem de hangi konumun filtrelendiğini gönder
        return view('uyarilar', [
            'alerts' => $alerts,
            'filteredLocation' => $location,
            'openWeatherApiKey' => env('OPENWEATHER_API_KEY') // API anahtarını view'a gönderiyoruz
        ]);
    }
}
