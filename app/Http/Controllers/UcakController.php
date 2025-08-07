<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UcakController extends Controller
{
    public function index()
{
    return view('ucak.index'); // blade dosyasının adı neyse
}

    
    public function startMotor()
{
    if (!in_array(auth()->user()->role, ['admin', 'itfaiye'])) {
        abort(403, 'Bu işlemi yapma yetkiniz yok.');
    }

    try {
        $esp32_ip = 'http://192.168.1.50/start'; // Gerekirse güncelle

        $response = Http::get($esp32_ip);

        if ($response->successful()) {
            return back()->with('success', 'Uçak motoru başarıyla çalıştırıldı.');
        } else {
            return back()->with('error', 'ESP32 cevap vermedi.');
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Bağlantı kurulamadı: ' . $e->getMessage());
    }
}
public function durdur(Request $request)
{
    try {
        $response = Http::timeout(5)->get('http://192.168.1.50/stop-motor'); // ESP32 durdurma endpointi

        if ($response->successful()) {
            return back()->with('success', 'Uçak motoru durduruldu.');
        } else {
            return back()->with('error', 'Uçak ile bağlantı kurulamadı.');
        }

    } catch (\Exception $e) {
        return back()->with('error', 'Hata oluştu: ' . $e->getMessage());
    }
}

}
