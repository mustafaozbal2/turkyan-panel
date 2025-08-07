<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf; // üstte ekle



use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index()
{
    return view('info.index');
}
public function pdf()
{
    $data = [
        'items' => [
            ['title' => '112’yi arayın', 'desc' => 'Yangını gördüğünüz anda acil çağrı yapın.'],
            ['title' => 'Bölgeyi boşaltın', 'desc' => 'Çevrenizi uyarın ve güvenli alana geçin.'],
            ['title' => 'Ekipmanınızı alın', 'desc' => 'Gönüllü iseniz liderinizle hareket edin.'],
            ['title' => 'Sakin olun', 'desc' => 'Paniklemeden çevrenize yardım edin.'],
        ]
    ];
    $pdf = Pdf::loadView('info.pdf', $data);
    return $pdf->stream('yangin-bilgilendirme.pdf');
}
}
