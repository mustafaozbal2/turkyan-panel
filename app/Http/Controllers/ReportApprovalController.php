<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VolunteerReport;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ReportApprovalController extends Controller
{
    /**
     * SADECE onay bekleyen raporları listeler.
     */
    public function index()
    {
        $firefighterId = Auth::id();

        // Sadece onay bekleyen raporları al, başka hiçbir şey hesaplama.
        $reports = VolunteerReport::with('user')
                                  ->where('assigned_user_id', $firefighterId)
                                  ->where('status', 'onay_bekliyor')
                                  ->latest()
                                  ->get();

        // Verileri YENİ ve TEMİZ arayüz dosyasına gönder.
        return view('onaylanacak-ihbarlar', compact('reports'));
    }

    /**
     * Bir gönüllü raporunu onaylar.
     */
    public function approve(VolunteerReport $report)
    {
        $report->status = 'onaylandi';
        $report->save();
        if ($report->user) { $report->user->increment('trust_score', 10); }
        Incident::create([
            'name' => 'Gönüllü İhbarı - ' . ($report->user->name ?? 'Bilinmeyen Kullanıcı'),
            'location' => 'Bilinmiyor', 'severity' => 'Belirlenmedi', 'status' => 'aktif',
            'latitude' => $report->latitude, 'longitude' => $report->longitude,
            'area_hectares' => 0, 'response_time_minutes' => 0,
        ]);
        return redirect('/onaylanacak-ihbarlar')->with('success', 'İhbar onaylandı ve yeni vaka oluşturuldu.');
    }

    /**
     * Bir gönüllü raporunu reddeder.
     */
    public function reject(VolunteerReport $report)
    {
        $report->status = 'reddedildi';
        $report->save();
        if ($report->user) { $report->user->decrement('trust_score', 5); }
        return redirect('/onaylanacak-ihbarlar')->with('warning', 'İhbar reddedildi.');
    }
}