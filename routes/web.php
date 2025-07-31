<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BakanlikController; // Bu satır önemli
use App\Http\Controllers\HaritaController;   // Bu satır önemli
// routes/web.php dosyasının üstüne ekleyin
use App\Http\Controllers\NewsController;

// ...

// --- NORMAL KULLANICILAR (GÖNÜLLÜLER) İÇİN ALAN ---
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::get('/api/incidents', [IncidentController::class, 'getActiveIncidentsAsGeoJson']);

    // YENİ HABER ROTALARI
    Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
    Route::get('/haberler/{slug}', [NewsController::class, 'show'])->name('news.show');
});

// --- NORMAL KULLANICILAR (GÖNÜLLÜLER) İÇİN ALAN ---
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // YENİ API ROTASI
    Route::get('/api/incidents', [IncidentController::class, 'getActiveIncidentsAsGeoJson']);
});
// --- Kimlik Doğrulama Rotaları ---
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// --- Ana Uygulama Rotaları ---
Route::get('/', fn() => redirect('/index'));

// --- YETKİLENDİRİLMİŞ KULLANICILAR İÇİN KORUMALI ALAN ---
Route::middleware(['auth', 'role:admin,bakanlik,itfaiye'])->group(function () {
    Route::get('/index', [DashboardController::class, 'index'])->name('index');
    Route::get('/harita', [HaritaController::class, 'index'])->name('harita');
    Route::get('/uyarilar', [AlertController::class, 'index'])->name('uyarilar');
    Route::get('/raporlar', [ReportController::class, 'index'])->name('raporlar');
    Route::view('/hakkimizda', 'hakkimizda')->name('hakkimizda');
    Route::get('/bakanlik', [BakanlikController::class, 'index'])->name('bakanlik');
});

// --- NORMAL KULLANICILAR (GÖNÜLLÜLER) İÇİN ALAN ---
Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
