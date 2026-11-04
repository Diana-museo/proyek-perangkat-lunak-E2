<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Middleware\CheckExecutiveRole;

// Pasang middleware CheckExecutiveRole di route dasbor
Route::middleware([CheckExecutiveRole::class])->group(function () {
    Route::get('/', [DashboardController::class, 'renderLiveDashboard']);
    Route::get('/dashboard-eksekutif/rincian', function () {
        return view('dashboard.drilldown');
    });
});

// Mengubah halaman utama (/) langsung menampilkan live dashboard
Route::get('/', [DashboardController::class, 'renderLiveDashboard']);

// Route untuk rincian drill-down (UI-KE2-05)
Route::get('/dashboard-eksekutif/rincian', function () {
    return view('dashboard.drilldown');
});


