<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BakanlikController;
use App\Http\Controllers\HaritaController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\MessageController;
// Login ve Register Controller'ları artık Auth::routes() tarafından yönetildiği için manuel use satırları kaldırıldı.

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Bu, projenizin nihai ve en temiz rota dosyasıdır.
*/

// --- KİMLİK DOĞRULAMA ROTALARI ---
// Bu tek satır, login, register, logout ve şifre sıfırlama gibi
// tüm standart kimlik doğrulama rotalarını bizim için otomatik olarak ekler.
Auth::routes();


// --- ANA YÖNLENDİRME ---
// Bu rota, giriş yapmış kullanıcıları rollerine göre doğru panele yönlendirir.
Route::get('/', function() {
    if (Auth::check()) {
        $role = Auth::user()->role;
        switch ($role) {
            case 'admin':
            case 'itfaiye':
                return redirect()->route('index');
            case 'bakanlik':
                return redirect()->route('bakanlik');
            default: // 'user' ve diğerleri
                return redirect()->route('dashboard');
        }
    }
    // Giriş yapmamışsa login sayfasına gider.
    return redirect()->route('login');
})->name('home');


// --- KORUMALI SAYFALAR (Giriş yapmış olmayı gerektiren tüm sayfalar) ---
Route::middleware(['auth'])->group(function () {

    // İTFAİYE VE ADMİN PANELLERİ
    Route::middleware('role:admin,itfaiye')->group(function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('index');
        Route::get('/raporlar', [ReportController::class, 'index'])->name('raporlar');
        Route::get('/uyarilar', [AlertController::class, 'index'])->name('uyarilar');
        Route::get('/harita', [HaritaController::class, 'index'])->name('harita');
    });

    // SADECE ADMİN'İN GÖRECEĞİ SAYFALAR
    Route::middleware('role:admin')->group(function () {
        Route::get('/hakkimizda', [AboutController::class, 'index'])->name('hakkimizda');
    });

    // BAKANLIK PANELİ (Admin de görebilir)
    Route::middleware('role:admin,bakanlik')->group(function () {
        Route::get('/bakanlik', [BakanlikController::class, 'index'])->name('bakanlik');
    });

    // GÖNÜLLÜ PANELİ (Admin de görebilir)
    Route::middleware('role:user,admin')->group(function () {
        Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');
        Route::get('/api/incidents', [IncidentController::class, 'getActiveIncidentsAsGeoJson']);
    });
    
    // HABER EKLEME/YÖNETME (Sadece Admin ve Bakanlık)
    // Daha spesifik olan bu kural, genel olanın ÜSTÜNDE olmalı
    Route::middleware('role:admin,bakanlik')->group(function () {
        Route::get('/haberler/yeni', [NewsController::class, 'create'])->name('news.create');
        Route::post('/haberler', [NewsController::class, 'store'])->name('news.store');
    });
    
    // HABERLERİ GÖRÜNTÜLEME (Tüm roller görebilir)
    Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
    Route::get('/haberler/{slug}', [NewsController::class, 'show'])->name('news.show');

    // MESAJLAŞMA SİSTEMİ (Tüm roller erişebilir)
    Route::get('/mesajlar/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/mesajlar', [MessageController::class, 'store'])->name('messages.store');

});