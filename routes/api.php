<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CashFlowController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes - Modul E-2 Arus Kas dan Dasbor Keuangan
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Rute API Modul E-2 (Pure Read-Only Aggregator)
    // 1. Ringkasan Eksekutif & Net Cash Flow
    Route::get('/dashboard/aggregated-summary', [DashboardController::class, 'aggregatedSummary']);
    
    // 2. Trend Arus Kas Bulanan
    Route::get('/cashflow/monthly', [DashboardController::class, 'monthlyTrend']);
    
    // 3. Detail Profitabilitas Proyek Drill-Down
    Route::get('/cashflow/project/{projectId}', [DashboardController::class, 'projectDetail']);
    
    // 4. Indikator Kesehatan Finansial
    Route::get('/dashboard/financial-indicators', [DashboardController::class, 'financialIndicators']);
});