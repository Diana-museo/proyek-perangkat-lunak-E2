<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CashFlowController;

/*
|--------------------------------------------------------------------------
| API Routes - Modul E-2 Arus Kas dan Dasbor Keuangan
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Rute dasar pencatatan dan ringkasan lokal E-2
    Route::get('/cashflow/summary', [CashFlowController::class, 'summary']);
    Route::post('/cashflow', [CashFlowController::class, 'store']);
    Route::get('/cashflow/project/{projectId}', [CashFlowController::class, 'getByProject']);
    Route::get('/dashboard/financial-indicators', [CashFlowController::class, 'financialIndicators']);
    Route::get('/cashflow/monthly', [CashFlowController::class, 'monthlyReport']);
    Route::get('/dashboard/aggregated-summary', [CashFlowController::class, 'aggregatedDashboard']);

    // Rute internal E-2 (BL-04 & BL-13)
    Route::post('/pemasukan-luar-jadwal', [CashFlowController::class, 'storePemasukanLuarJadwal']);
    Route::post('/pengeluaran-operasional', [CashFlowController::class, 'storePengeluaranOperasional']);
});