<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Services\ErpIntegrationService;
use Illuminate\Http\Request;
use Exception;

class CashFlowController extends Controller
{
    // 1. Summary Lokal
    public function summary()
    {
        $totalIncome = (float) CashFlow::where('type', 'income')->sum('amount');
        $totalExpense = (float) CashFlow::where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Ringkasan arus kas modul E-2',
            'data' => [
                'total_income' => number_format($totalIncome, 2, '.', ''),
                'total_expense' => number_format($totalExpense, 2, '.', ''),
                'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
                'currency' => 'IDR'
            ]
        ], 200);
    }

    // 2. Simpan Transaksi Lokal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|string|unique:cash_flows',
            'project_id' => 'nullable|string',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $cashFlow = CashFlow::create($validated);

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => 'Data arus kas berhasil dicatat',
            'data' => $cashFlow
        ], 201);
    }

    // 3. Filter per Proyek
    public function getByProject($projectId)
    {
        $cashFlows = CashFlow::where('project_id', $projectId)->get();

        $totalIncome = (float) $cashFlows->where('type', 'income')->sum('amount');
        $totalExpense = (float) $cashFlows->where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Detail arus kas untuk proyek: ' . $projectId,
            'data' => [
                'project_id' => $projectId,
                'total_income' => number_format($totalIncome, 2, '.', ''),
                'total_expense' => number_format($totalExpense, 2, '.', ''),
                'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
                'transactions' => $cashFlows
            ]
        ], 200);
    }

    // 4. Indikator Keuangan
    public function financialIndicators()
    {
        $totalIncome = (float) CashFlow::where('type', 'income')->sum('amount');
        $totalExpense = (float) CashFlow::where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Indikator keuangan manajemen dan direksi',
            'data' => [
                'realized_income' => number_format($totalIncome, 2, '.', ''),
                'realized_expense' => number_format($totalExpense, 2, '.', ''),
                'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
                'financial_health' => $netCashFlow >= 0 ? 'SEHAT (SURPLUS)' : 'DEFISIT',
                'currency' => 'IDR'
            ]
        ], 200);
    }

    // 5. Rekap Bulanan
    public function monthlyReport(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $cashFlows = CashFlow::whereYear('transaction_date', $year)->get();

        $monthlyData = $cashFlows->groupBy(function($item) {
            return date('F', strtotime($item->transaction_date));
        })->map(function ($group) {
            $income = (float) $group->where('type', 'income')->sum('amount');
            $expense = (float) $group->where('type', 'expense')->sum('amount');
            $net = $income - $expense;
            return [
                'total_income' => number_format($income, 2, '.', ''),
                'total_expense' => number_format($expense, 2, '.', ''),
                'net' => number_format($net, 2, '.', ''),
                'transaction_count' => $group->count()
            ];
        });

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Rekap arus kas bulanan tahun ' . $year,
            'data' => $monthlyData
        ], 200);
    }

    // 6. Dasbor Agregat dari Modul Lain (D-1, D-2, E-1, F-5)
    public function aggregatedDashboard(Request $request)
    {
        try {
            $integration = new ErpIntegrationService($request->bearerToken());

            $projectId = $request->input('projectId');
            $taxPeriod = $request->input('taxPeriod', '2026-11');
            $periodeId = $request->input('periodeId');

            $projectData = $integration->getProject($projectId);
            $termins = $integration->getTermins($projectId);

            $projectedIncome = 0;
            if (is_array($termins)) {
                foreach ($termins as $item) {
                    $projectedIncome += (float) ($item['terminValue'] ?? 0);
                }
            }

            $taxSummary = $integration->getTaxSummary($taxPeriod, $projectId);
            $totalTaxExpense = $taxSummary ? (float) ($taxSummary['totalPpn'] ?? 0) : 0.00;

            $payrollData = $integration->getPayrollRekap($periodeId);
            $totalPayrollExpense = $payrollData ? (float) ($payrollData['totalGajiBersih'] ?? 0) : 0.00;

            $netCashFlow = $projectedIncome - ($totalTaxExpense + $totalPayrollExpense);

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => 'Agregat dasbor keuangan manajemen E-2 dari modul D-1, D-2, E-1, F-5',
                'data' => [
                    'project_info' => $projectData,
                    'projected_income_from_d2' => number_format($projectedIncome, 2, '.', ''),
                    'tax_expense_from_e1' => number_format($totalTaxExpense, 2, '.', ''),
                    'payroll_expense_from_f5' => number_format($totalPayrollExpense, 2, '.', ''),
                    'net_cash_flow' => number_format($netCashFlow, 2, '.', ''),
                    'financial_health' => $netCashFlow >= 0 ? 'SEHAT (SURPLUS)' : 'DEFISIT',
                    'currency' => 'IDR'
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'code' => 503,
                'message' => 'ERR-DS-503: Data belum tersedia. Gagal menyinkronkan data dari modul sumber: ' . $e->getMessage()
            ], 503);
        }
    }

    // Pencatatan Pemasukan Luar Jadwal (BL-04)
    public function storePemasukanLuarJadwal(Request $request)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:0.01',
            'tanggalTransaksi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $cashFlow = new CashFlow();
        $cashFlow->transaction_id = 'pms-manual-' . uniqid();
        $cashFlow->type = 'income';
        $cashFlow->amount = $validated['nominal'];
        $cashFlow->category = 'Pemasukan Luar Jadwal';
        $cashFlow->transaction_date = $validated['tanggalTransaksi'];
        $cashFlow->description = $validated['keterangan'] ?? '-';
        $cashFlow->save();

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => 'Data pemasukan luar jadwal berhasil disimpan',
            'data' => [
                'pemasukanId' => $cashFlow->transaction_id,
                'nominal' => (float)$cashFlow->amount,
                'tanggalTransaksi' => $cashFlow->transaction_date,
                'keterangan' => $cashFlow->description
            ]
        ], 201);
    }

    // Pencatatan Pengeluaran Operasional (BL-13)
    public function storePengeluaranOperasional(Request $request)
    {
        $validated = $request->validate([
            'nominal' => 'required|numeric|min:0.01',
            'kategori' => 'required|string',
            'tanggalTransaksi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $cashFlow = new CashFlow();
        $cashFlow->transaction_id = 'exp-manual-' . uniqid();
        $cashFlow->type = 'expense';
        $cashFlow->amount = $validated['nominal'];
        $cashFlow->category = 'Operasional: ' . $validated['kategori'];
        $cashFlow->transaction_date = $validated['tanggalTransaksi'];
        $cashFlow->description = $validated['keterangan'] ?? '-';
        $cashFlow->save();

        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => 'Pengeluaran operasional berhasil dicatat',
            'data' => [
                'pengeluaranId' => $cashFlow->transaction_id,
                'nominal' => (float)$cashFlow->amount,
                'kategori' => $validated['kategori'],
                'tanggalTransaksi' => $cashFlow->transaction_date,
                'keterangan' => $cashFlow->description
            ]
        ], 201);
    }
}
