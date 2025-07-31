<?php

namespace App\Http\Controllers;

use App\Models\Alert; // Alert modelini kullanacağımızı belirtiyoruz
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Tüm uyarıları listeler ve sayfaya gönderir.
     */
    public function index()
    {
        // Veritabanından tüm uyarıları en yeniden en eskiye doğru sıralayarak al
        $alerts = Alert::latest()->get();

        // 'uyarilar' view'ını aç ve '$alerts' değişkenini ona gönder
        return view('uyarilar', [
            'alerts' => $alerts
        ]);
    }
}