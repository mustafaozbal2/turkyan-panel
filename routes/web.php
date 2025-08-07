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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\UcakController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VolunteerReportController;
use App\Http\Controllers\ReportApprovalController;

Auth::routes();

Route::get('/', function() {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    $role = Auth::user()->role;
    switch ($role) {
        case 'admin':
        case 'itfaiye':
            return redirect()->route('index');
        case 'bakanlik':
            return redirect()->route('bakanlik');
        default:
            return redirect()->route('dashboard');
    }
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/ihbar/yeni', [VolunteerReportController::class, 'create'])->name('volunteer.report.create');
    Route::post('/ihbar', [VolunteerReportController::class, 'store'])->name('volunteer.report.store');
    Route::post('/ihbar/{report}/handle', [VolunteerReportController::class, 'handleReportAction'])->name('volunteer.report.handle');

    Route::get('/uyarilar', [AlertController::class, 'index'])->name('uyarilar');

    Route::get('/haberler/yeni', [NewsController::class, 'create'])->name('news.create');
    Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
    Route::get('/haberler/{slug}', [NewsController::class, 'show'])->name('news.show');
    Route::post('/haberler', [NewsController::class, 'store'])->name('news.store');

    Route::get('/bilgilendirme', [InfoController::class, 'index'])->name('info.page');
    Route::get('/bilgilendirme/pdf', [InfoController::class, 'pdf'])->name('info.pdf');

    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
    Route::get('/api/incidents', [IncidentController::class, 'getActiveIncidentsAsGeoJson']);

    // --- MESAJLAŞMA ---
    Route::get('/sohbetler', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/sohbet/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/sohbet/{user}', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/mesajlar/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/mesajlar', [MessageController::class, 'store'])->name('messages.store');

    // ADMİN + İTFAİYE
    Route::middleware('role:admin,itfaiye')->group(function () {
        Route::get('/index', [DashboardController::class, 'index'])->name('index');
        Route::get('/raporlar', [ReportController::class, 'index'])->name('raporlar');
        Route::get('/harita', [HaritaController::class, 'index'])->name('harita');

        Route::get('/ucak', function () {
            return view('ucak.index');
        })->name('ucak.sayfa');

        Route::post('/ucak/baslat', [UcakController::class, 'startMotor'])->name('ucak.baslat');
        Route::post('/ucak/durdur', [UcakController::class, 'durdur'])->name('ucak.durdur');
        Route::post('/incidents/{incident}/handle', [DashboardController::class, 'handleIncidentAction'])->name('incidents.handle');

        // 🔁 BURASI DÜZENLENDİ
Route::get('/onaylanacak-ihbarlar', [ReportApprovalController::class, 'index'])->name('reports.pending');        Route::get('/ihbarlar', [VolunteerReportController::class, 'index'])->name('volunteer.reports.index');
    });

    // SADECE ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/hakkimizda', [AboutController::class, 'index'])->name('hakkimizda');
    });

    // BAKANLIK
    Route::middleware('role:admin,bakanlik')->group(function () {
        Route::get('/bakanlik', [BakanlikController::class, 'index'])->name('bakanlik');
    });
 Route::post('/raporlar/{report}/onayla', [ReportApprovalController::class, 'approve'])->name('reports.approve');
    Route::post('/raporlar/{report}/reddet', [ReportApprovalController::class, 'reject'])->name('reports.reject');
    // GÖNÜLLÜ
    Route::middleware('role:user,admin')->group(function () {
        Route::get('/dashboard', function() {
            return view('dashboard');
        })->name('dashboard');
    });
});
