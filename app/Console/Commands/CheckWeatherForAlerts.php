<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Alert;
use App\Models\FireStation;
use Carbon\Carbon;

class CheckWeatherForAlerts extends Command
{
    protected $signature = 'weather:check-alerts';
    protected $description = 'Gelişmiş hava durumu analizi yaparak kritik koşullar için uyarılar oluşturur.';

    // Test modunu kapatıyoruz. Artık sadece gerçek riskler raporlanacak.
    const TEST_MODE = false;

    public function handle()
    {
        $this->info('Gelişmiş Hava Durumu Analizi Başlatıldı...');
        $locations = FireStation::all();

        if (!env('OPENWEATHER_API_KEY')) {
            $this->error('HATA: Lütfen .env dosyanıza OPENWEATHER_API_KEY değişkenini ekleyin.');
            return 1;
        }

        foreach ($locations as $location) {
            $response = Http::get('https://api.openweathermap.org/data/2.5/forecast', [
                'lat' => $location->latitude,
                'lon' => $location->longitude,
                'appid' => env('OPENWEATHER_API_KEY'),
                'units' => 'metric',
                'lang' => 'tr'
            ]);

            if ($response->successful()) {
                // Sadece önümüzdeki 3 güne odaklanalım
                $threeDaysFromNow = Carbon::now()->addDays(3)->timestamp;
                $forecasts = array_filter(
                    $response->json()['list'],
                    fn($forecast) => $forecast['dt'] <= $threeDaysFromNow
                );

                // --- RİSK ANALİZİ FONKSİYONLARI ---
                $this->checkForExtremeRain($forecasts, $location);
                $this->checkForDrought($forecasts, $location);
                $this->checkForHail($forecasts, $location);
                $this->checkForSnow($forecasts, $location);

            }
        }
        $this->info('Analiz başarıyla tamamlandı.');
        return 0;
    }

    /**
     * Aşırı Yağış (Sel Riski) Tespiti
     * Kural: 3 saat içinde 15mm'den fazla yağış.
     */
    private function checkForExtremeRain($forecasts, $location)
    {
        $rainThreshold = self::TEST_MODE ? 0.1 : 15;
        foreach ($forecasts as $forecast) {
            if (isset($forecast['rain']['3h']) && $forecast['rain']['3h'] > $rainThreshold) {
                $this->createAlert(
                    'Yüksek', 'fa-cloud-showers-heavy', 'Kuvvetli Yağış Uyarısı', $location,
                    "Tahmini zaman: ".Carbon::createFromTimestamp($forecast['dt'])->format('d.m.Y H:i').". Bölgede şiddetli yağış riski bulunmaktadır. Sel ve su baskınlarına karşı dikkatli olunmalıdır."
                );
                return; // İlk tespitte uyarıyı oluştur ve bu konum için başka yağmur kontrolü yapma.
            }
        }
    }

    /**
     * Aşırı Kuraklık (Yangın Riski) Tespiti
     * Kural: En az 2 ardışık gün boyunca gündüz sıcaklığı 35°C üzeri VE nem %25 altı.
     */
    private function checkForDrought($forecasts, $location)
    {
        $tempThreshold = self::TEST_MODE ? 15 : 35;
        $humidityThreshold = self::TEST_MODE ? 80 : 25;
        $hotDryDaysInARow = 0;
        $maxHotDryDays = 0;

        $dailyForecasts = [];
        foreach ($forecasts as $forecast) {
            $dailyForecasts[date('Y-m-d', $forecast['dt'])][] = $forecast;
        }

        foreach ($dailyForecasts as $hourlyData) {
            $isHotAndDry = false;
            foreach ($hourlyData as $hour) {
                if (date('H', $hour['dt']) >= 9 && date('H', $hour['dt']) <= 18) {
                    if ($hour['main']['temp'] > $tempThreshold && $hour['main']['humidity'] < $humidityThreshold) {
                        $isHotAndDry = true; break;
                    }
                }
            }
            $hotDryDaysInARow = $isHotAndDry ? $hotDryDaysInARow + 1 : 0;
            if ($hotDryDaysInARow > $maxHotDryDays) $maxHotDryDays = $hotDryDaysInARow;
        }

        if ($maxHotDryDays >= 2) {
            $this->createAlert(
                'Kritik', 'fa-fire-alt', 'Yüksek Sıcaklık ve Yangın Riski', $location,
                "Bölgede {$maxHotDryDays} gün sürmesi beklenen aşırı sıcak ve kuru hava nedeniyle orman yangını riski kritik seviyededir."
            );
        }
    }

    /**
     * Dolu Yağışı Tespiti
     * Kural: OpenWeather fırtına ve dolu kodlarını (2xx, 511, 906) kontrol et.
     */
    private function checkForHail($forecasts, $location)
    {
        $hailCodes = [511, 906]; // 511: Donan Yağmur, 906: Dolu
        foreach ($forecasts as $forecast) {
            $weatherId = $forecast['weather'][0]['id'];
            if (in_array($weatherId, $hailCodes) || ($weatherId >= 200 && $weatherId <= 232)) { // Fırtına kodları da risklidir
                $this->createAlert(
                    'Yüksek', 'fa-cloud-meatball', 'Dolu ve Fırtınalı Hava Uyarısı', $location,
                    "Tahmini zaman: ".Carbon::createFromTimestamp($forecast['dt'])->format('d.m.Y H:i').". Bölgede tarım alanlarına ve araçlara zarar verebilecek dolu yağışı riski bulunmaktadır."
                );
                return;
            }
        }
    }

    /**
     * Yoğun Kar Yağışı Tespiti
     * Kural: OpenWeather kar kodlarını (6xx) kontrol et.
     */
    private function checkForSnow($forecasts, $location)
    {
        foreach ($forecasts as $forecast) {
            $weatherId = $forecast['weather'][0]['id'];
            if ($weatherId >= 600 && $weatherId <= 622) {
                $this->createAlert(
                    'Orta', 'fa-snowflake', 'Yoğun Kar Yağışı Uyarısı', $location,
                    "Tahmini zaman: ".Carbon::createFromTimestamp($forecast['dt'])->format('d.m.Y H:i').". Bölgede ulaşımda aksamalara neden olabilecek kar yağışı beklenmektedir."
                );
                return;
            }
        }
    }

    /**
     * Veritabanına tekrarı önleyerek yeni bir uyarı kaydı oluşturur.
     */
    private function createAlert(string $severity, string $icon, string $title, $location, string $desc)
    {
        // Benzer bir uyarının son 3 günde oluşturulup oluşturulmadığını kontrol et
        $existingAlert = Alert::where('title', $title)
                              ->where('location', $location->name)
                              ->where('created_at', '>=', now()->subDays(3))
                              ->first();

        if (!$existingAlert) {
            Alert::create(compact('severity', 'icon', 'title', 'desc') + ['location' => $location->name]);
            $this->info("✅ UYARI OLUŞTURULDU: {$location->name} - {$title}");
        }
    }
}
