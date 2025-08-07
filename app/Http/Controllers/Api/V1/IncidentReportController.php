<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IncidentReportController extends Controller
{
    /**
     * Yapay zeka servisinden gelen yeni olay tespitlerini işler.
     */
    public function store(Request $request)
    {
        // 1. Gelen Veriyi Doğrulama (Validation)
        // API'mizin sadece beklediğimiz formatta veri kabul etmesini sağlıyoruz.
        $validator = Validator::make($request->all(), [
            'latitude'           => ['required', 'numeric', 'between:-90,90'],
            'longitude'          => ['required', 'numeric', 'between:-180,180'],
            'confidence_score'   => ['required', 'numeric', 'between:0,1'],
            'estimated_size'     => ['required', 'string', Rule::in(['small', 'medium', 'large'])],
            'image_url'          => ['required', 'url'],
            'timestamp'          => ['nullable', 'date_format:Y-m-d\TH:i:s\Z'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // Hatalı veri formatı
        }

        // 2. Veritabanına Kaydetme
        // Doğrulanmış verileri alıyoruz.
        $validatedData = $validator->validated();

        $incident = Incident::create([
            // Bu alanları manuel olarak dolduruyoruz
            'name'               => 'AI Tespit Edilen Olay', // Varsayılan bir isim
            'location'           => 'Bilinmiyor', // Daha sonra koordinattan şehire çevrilebilir
            'severity'           => 'Belirlenmedi', // Onay sonrası belirlenecek
            'response_time_minutes' => 0, // Onay sonrası hesaplanacak
            'area_hectares'      => 0, // Onay sonrası girilecek

            // Bu alanlar doğrudan API'den geliyor
            'latitude'           => $validatedData['latitude'],
            'longitude'          => $validatedData['longitude'],
            'status'             => 'onay_bekliyor', // En önemli kısım: Olayı onay bekliyor olarak işaretliyoruz
            'confidence_score'   => $validatedData['confidence_score'],
            'estimated_size'     => $validatedData['estimated_size'],
            'evidence_image_url' => $validatedData['image_url'],

            // Eğer yapay zeka tespit zamanını gönderdiyse onu, göndermediyse şu anki zamanı kullan
            'created_at'         => $validatedData['timestamp'] ?? now(),
            'updated_at'         => $validatedData['timestamp'] ?? now(),
        ]);

        // 3. Başarılı Cevabı Döndürme
        // Yapay zeka servisine her şeyin yolunda gittiğini bildiriyoruz.
        return response()->json([
            'message' => 'Olay başarıyla alındı ve onaya gönderildi.',
            'incident_id' => $incident->id
        ], 201); // 201 Created: Kaynak başarıyla oluşturuldu
    }
}