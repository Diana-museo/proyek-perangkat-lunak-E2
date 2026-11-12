<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Middleware\CheckExecutiveRole;

// 1. Rute Autentikasi Login (Tanpa Middleware, agar bisa diakses saat belum login)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Pindahkan rute 403 ke sini agar bebas diakses saat sesi ditolak/dialihkan
Route::get('/auth/forbidden-access', function () {
    return view('auth.forbidden-access');
})->name('error.403');

/*
|--------------------------------------------------------------------------
| Web Routes - Modul E-2 Arus Kas dan Dasbor Keuangan
|--------------------------------------------------------------------------
| Catatan: Autentikasi dan verifikasi peran sepenuhnya mengandalkan
| integrasi token validasi dari Modul F-1 (Identity & Access Management).
*/

// Rute Utama & Dasbor (Diproteksi Middleware CheckExecutiveRole yang memverifikasi token ke F-1)
Route::middleware([CheckExecutiveRole::class])->group(function () {
    
    // Halaman Utama / Dasbor Utama (Menampilkan ringkasan eksekutif & tabel termin D-2)
    Route::get('/', [DashboardController::class, 'renderLiveDashboard']);
    Route::get('/dashboard', [DashboardController::class, 'renderLiveDashboard']);
    Route::get('/dashboard/pemasukan', [DashboardController::class, 'renderPemasukan']);
    Route::get('/dashboard/pengeluaran', [DashboardController::class, 'renderPengeluaran']);
    Route::get('/dashboard/proyek', [DashboardController::class, 'renderProyek']);
    Route::get('/dashboard/api/refresh', [DashboardController::class, 'refreshData']);

    // Rute Rincian Drill-down Proyek (UI-KE2-05) dengan parameter opsional {projectId?}
    Route::get('/dashboard-eksekutif/rincian/{projectId?}', function ($projectId = null) {
        // Mengarahkan ke method drilldown di controller atau view rincian
        return view('dashboard.drilldown', ['projectId' => $projectId]);
    });
});

// // 2. Rute Utama & Dasbor (Diproteksi Middleware CheckExecutiveRole & Fallback F-1)
// Route::middleware([CheckExecutiveRole::class])->group(function () {
//     // Halaman Utama / Dashboard Live E-2
//     Route::get('/', [DashboardController::class, 'renderLiveDashboard']);
//     Route::get('/dashboard', [DashboardController::class, 'renderLiveDashboard']);

//     // Rute untuk rincian drill-down (UI-KE2-05) mendukung parameter opsional {projectId?}
//     Route::get('/dashboard-eksekutif/rincian/{projectId?}', [DashboardController::class, 'renderDrilldown']);
// });

// // Mengubah halaman utama (/) langsung menampilkan live dashboard
// Route::get('/', [DashboardController::class, 'renderLiveDashboard']);

// // Route untuk rincian drill-down (UI-KE2-05)
// Route::get('/dashboard-eksekutif/rincian', function () {
//     return view('dashboard.drilldown');
// });


