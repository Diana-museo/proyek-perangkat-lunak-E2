<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ErpIntegrationService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Helper untuk memvalidasi token atau hak akses (Simulasi F-1 Middleware/Check)
     */
    private function validateAuth(Request $request)
    {
        $token = $request->bearerToken();

        // 1. Cek keberadaan token (401 Unauthorized)
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-AUTH-401',
                'message' => 'Token akses tidak valid atau kedaluwarsa.'
            ], 401);
        }

        // Simulasi jika token ditolak/role bukan eksekutif (403 Forbidden)
        if ($token === 'invalid-token') {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-AUTH-403',
                'message' => 'Akun pengguna tidak memiliki hak akses tingkat eksekutif.'
            ], 403);
        }

        return null;
    }

    /**
     * Endpoint Halaman 1: Ringkasan Eksekutif & Net Cash Flow
     * GET /api/v1/dashboard/aggregated-summary
     */
    public function aggregatedSummary(Request $request)
    {
        if ($authError = $this->validateAuth($request)) {
            return $authError;
        }

        try {
            // Paksa Error
            // throw new \Exception("Koneksi ke server hulu E-1 Pajak mengalami timeout (2000ms).");

            $token = $request->bearerToken();
            $erpService = new ErpIntegrationService($token);

            $taxPeriod = $request->query('taxPeriod', '2026-11');
            $periodeId = $request->query('periodeId', '01a1193d-2500-7c04-854b-8bf03c711dde');
            $projectId = $request->query('projectId', null);

            $termins = $erpService->getTermins($projectId);
            $taxSummary = $erpService->getTaxSummary($taxPeriod, $projectId);
            $payroll = $erpService->getPayrollRekap($periodeId);

            $totalProjectedIncome = 0;
            foreach ($termins as $termin) {
                $totalProjectedIncome += floatval($termin['terminValue'] ?? 0);
            }

            $totalPpn = floatval($taxSummary['totalPpn'] ?? 0);
            $totalPpnbm = floatval($taxSummary['totalPpnbm'] ?? 0);
            $totalTaxExpense = $totalPpn + $totalPpnbm;

            $totalGajiBersih = floatval($payroll['totalGajiBersih'] ?? 0);
            $totalBpjsPerusahaan = floatval($payroll['totalBpjsPerusahaan'] ?? 0);
            $totalPayrollExpense = $totalGajiBersih + $totalBpjsPerusahaan;

            $netCashFlow = $totalProjectedIncome - ($totalTaxExpense + $totalPayrollExpense);

            return response()->json([
                'status' => 'success',
                'code' => 'E2-200',
                'message' => 'Agregasi dasbor eksekutif berhasil dimuat',
                'data' => [
                    'total_projected_income' => number_format($totalProjectedIncome, 2, '.', ''),
                    'total_tax_expense' => number_format($totalTaxExpense, 2, '.', ''),
                    'total_payroll_expense' => number_format($totalPayrollExpense, 2, '.', ''),
                    'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
                    'currency' => 'IDR',
                    'breakdown_sources' => [
                        'd2_termins_count' => count($termins),
                        'e1_tax_period' => $taxPeriod,
                        'f5_periode_id' => $periodeId
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-DS-503',
                'message' => 'Modul hulu mengalami kendala koneksi atau data belum final: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Endpoint Halaman 2: Cash Flow Summary & Monthly Trend
     * GET /api/v1/cashflow/monthly
     */
    public function monthlyTrend(Request $request)
    {
        if ($authError = $this->validateAuth($request)) {
            return $authError;
        }

        $year = $request->query('year', '2026');

        // Validasi parameter query tahun (400 Bad Request)
        if (!is_numeric($year) || strlen($year) !== 4) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-REQ-400',
                'message' => 'Parameter query year tidak valid. Gunakan format tahun 4 digit (misal: 2026).'
            ], 400);
        }

        try {
            $token = $request->bearerToken();
            $erpService = new ErpIntegrationService($token);
            $monthlyData = [];

            for ($m = 1; $m <= 12; $m++) {
                $monthStr = str_pad($m, 2, '0', STR_PAD_LEFT);
                $taxPeriod = "{$year}-{$monthStr}";
                $periodeId = "periode-{$year}-{$monthStr}";

                $taxSummary = $erpService->getTaxSummary($taxPeriod);
                $payroll = $erpService->getPayrollRekap($periodeId);

                $taxExpense = floatval($taxSummary['totalPpn'] ?? 0) + floatval($taxSummary['totalPpnbm'] ?? 0);
                $payrollExpense = floatval($payroll['totalGajiBersih'] ?? 0) + floatval($payroll['totalBpjsPerusahaan'] ?? 0);
                $income = floatval($payroll['totalTermin'] ?? 0); 
                $netFlow = $income - ($taxExpense + $payrollExpense);

                $monthlyData[] = [
                    'month' => $taxPeriod,
                    'total_income' => number_format($income, 2, '.', ''),
                    'total_tax_expense' => number_format($taxExpense, 2, '.', ''),
                    'total_payroll_expense' => number_format($payrollExpense, 2, '.', ''),
                    'net_cash_flow' => number_format($netFlow, 2, '.', ''),
                ];
            }

            return response()->json([
                'status' => 'success',
                'code' => 'E2-200',
                'message' => 'Data tren arus kas bulanan berhasil dimuat',
                'data' => [
                    'year' => $year,
                    'trend' => $monthlyData
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-DS-503',
                'message' => 'Gagal menyusun tren bulanan akibat kendala modul hulu: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Endpoint Halaman 3: Project Profitability Drill-Down
     * GET /api/v1/cashflow/project/{projectId}
     */
    public function projectDetail(Request $request, $projectId)
    {
        if ($authError = $this->validateAuth($request)) {
            return $authError;
        }

        // Validasi ketersediaan parameter (400 Bad Request)
        if (empty($projectId)) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-REQ-400',
                'message' => 'Parameter projectId wajib diisi.'
            ], 400);
        }

        try {
            $token = $request->bearerToken();
            $erpService = new ErpIntegrationService($token);

            $project = $erpService->getProject($projectId);

            // PAKSA TEST 404: Jika ingin ambil contoh error 404
            if ($projectId === 'proyek-tidak-ada' || !$project) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR-NOT-FOUND-404',
                    'message' => "Data proyek dengan ID '{$projectId}' tidak ditemukan."
                ], 404);
            }

            // Jika ID proyek tidak ditemukan di modul D-1 (404 Not Found)
            if (!$project) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR-NOT-FOUND-404',
                    'message' => "Data proyek dengan ID '{$projectId}' tidak ditemukan."
                ], 404);
            }

            $termins = $erpService->getTermins($projectId);
            $taxSummary = $erpService->getTaxSummary('2026-10', $projectId);

            $projectIncome = 0;
            foreach ($termins as $termin) {
                $projectIncome += floatval($termin['terminValue'] ?? 0);
            }

            $projectTax = floatval($taxSummary['totalPpn'] ?? 0) + floatval($taxSummary['totalPpnbm'] ?? 0);
            $projectNetProfit = $projectIncome - $projectTax;

            return response()->json([
                'status' => 'success',
                'code' => 'E2-200',
                'message' => 'Detail profitabilitas proyek berhasil dimuat',
                'data' => [
                    'project_metadata' => $project,
                    'financial_summary' => [
                        'total_project_income' => number_format($projectIncome, 2, '.', ''),
                        'total_project_tax' => number_format($projectTax, 2, '.', ''),
                        'net_project_profit' => number_format($projectNetProfit, 2, '.', ''),
                    ],
                    'raw_termins' => $termins
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-DS-503',
                'message' => 'Gagal memuat detail proyek dari modul hulu: ' . $e->getMessage()
            ], 503);
        }
    }

    /**
     * Endpoint Halaman 4: Financial Indicators & Health Status
     * GET /api/v1/dashboard/financial-indicators
     */
    public function financialIndicators(Request $request)
    {
        if ($authError = $this->validateAuth($request)) {
            return $authError;
        }

        try {
            $token = $request->bearerToken();
            $erpService = new ErpIntegrationService($token);

            $taxPeriod = $request->query('taxPeriod', '2026-10');
            $periodeId = $request->query('periodeId', '01a1193d-2500-7c04-854b-8bf03c711dde');

            $taxSummary = $erpService->getTaxSummary($taxPeriod);
            $payroll = $erpService->getPayrollRekap($periodeId);
            $termins = $erpService->getTermins();

            $totalIncome = 0;
            foreach ($termins as $termin) {
                $totalIncome += floatval($termin['terminValue'] ?? 0);
            }

            $totalTax = floatval($taxSummary['totalPpn'] ?? 0);
            $totalPayroll = floatval($payroll['totalGajiBersih'] ?? 0);
            $totalExpenses = $totalTax + $totalPayroll;

            $healthStatus = "HEALTHY";
            $indicatorMessage = "Arus kas dan beban operasional dalam kondisi seimbang.";

            if ($totalExpenses > $totalIncome) {
                $healthStatus = "WARNING";
                $indicatorMessage = "Perhatian: Total beban (pajak + payroll) melebihi proyeksi termin masuk.";
            }

            return response()->json([
                'status' => 'success',
                'code' => 'E2-200',
                'message' => 'Indikator kesehatan finansial berhasil dievaluasi',
                'data' => [
                    'health_status' => $healthStatus,
                    'description' => $indicatorMessage,
                    'metrics' => [
                        'tax_payment_status' => $taxSummary['paymentStatus'] ?? 'UNKNOWN',
                        'payroll_period_name' => $payroll['namaPeriode'] ?? 'Unknown',
                        'total_projected_inflow' => number_format($totalIncome, 2, '.', ''),
                        'total_outflow_expenses' => number_format($totalExpenses, 2, '.', ''),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-DS-503',
                'message' => 'Gagal mengagregasi indikator finansial: ' . $e->getMessage()
            ], 503);
        }
    }
}