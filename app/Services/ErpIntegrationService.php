<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ErpIntegrationService
{
    protected $token;

    public function __construct($token = null)
    {
        $this->token = $token;
    }

    // 1. Modul D-1 (Registrasi Proyek)
    public function getProject($projectId)
    {
        $projectId = $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567";
        $baseUrl = env('D1_SERVICE_URL', 'http://127.0.0.1:4010');
        
        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("{$baseUrl}/projects/{$projectId}");

            if ($response->successful()) {
                return $response->json('data') ?? $response->json();
            }
            
            Log::warning("D-1 Live Server mengembalikan status non-success: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("Gagal koneksi ke D-1 Live Server, beralih ke Mock. Error: " . $e->getMessage());
        }

        // FALLBACK: Mock Data D-1
        return [
            "projectId" => $projectId,
            "canonicalCode" => "202601",
            "projectName" => "Implementasi Sistem ERP Cloud Anaheim Tahap 1",
            "clientId" => "PTANTRI",
            "clientName" => "PT Anaheim Nimbus Universal",
            "poReceived" => true,
            "status" => "REGISTERED"
        ];
    }

    // 2. Modul D-2 (Kontrak & Termin)
    public function getTermins($projectId = null)
    {
        $baseUrl = env('D2_SERVICE_URL', 'http://localhost:8000');

        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("{$baseUrl}/api/v1/termins", [
                    'projectId' => $projectId
                ]);

            if ($response->successful()) {
                return $response->json('data.items') ?? [];
            }

            Log::warning("D-2 Live Server mengembalikan status non-success: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("Gagal koneksi ke D-2 Live Server, beralih ke Mock. Error: " . $e->getMessage());
        }

        // FALLBACK: Mock Data D-2
        return [
            [
                "terminId" => "trm-demo-001",
                "terminNumber" => 1,
                "terminValue" => "100000000.00",
                "dueDate" => "2026-10-15",
                "status" => "SIAP_DITAGIH",
                "projectId" => $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567"
            ],
            [
                "terminId" => "trm-demo-002",
                "terminNumber" => 2,
                "terminValue" => "150000000.00",
                "dueDate" => "2026-12-15",
                "status" => "SIAP_DITAGIH",
                "projectId" => $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567"
            ]
        ];
    }

    // 3. Modul E-1 (Pajak)
    public function getTaxSummary($taxPeriod, $projectId = null)
    {
        $baseUrl = env('E1_SERVICE_URL', 'http://localhost:8080');

        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("{$baseUrl}/api/v1/tax/summary", [
                    'taxPeriod' => $taxPeriod,
                    'projectId' => $projectId
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? $response->json();
            }

            Log::warning("E-1 Live Server mengembalikan status non-success: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("Gagal koneksi ke E-1 Live Server, beralih ke Mock. Error: " . $e->getMessage());
        }

        // FALLBACK: Mock Data E-1
        return [
            "taxPeriod" => $taxPeriod,
            "invoiceCount" => 1,
            "totalDpp" => "100000000.00",
            "totalPpn" => "11000000.00",
            "totalPpnbm" => "0.00",
            "paymentStatus" => "CONFIRMED"
        ];
    }

    // 4. Modul F-5 (Payroll)
    public function getPayrollRekap($periodeId)
    {
        $baseUrl = env('F5_SERVICE_URL', 'http://127.0.0.1:8000');

        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("{$baseUrl}/api/payroll/v1/rekap-biaya", [
                    'periodeId' => $periodeId
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning("F-5 Live Server mengembalikan status non-success: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("Gagal koneksi ke F-5 Live Server, beralih ke Mock. Error: " . $e->getMessage());
        }

        // FALLBACK: Mock Data F-5
        return [
            "periodeId" => $periodeId ?? "01a1193d-2500-7c04-854b-8bf03c711dde",
            "namaPeriode" => "Oktober 2026",
            "jumlahKaryawan" => 5,
            "totalGajiBersih" => 37425000.0,
            "totalBpjsPerusahaan" => 260000.0,
            "totalTermin" => 25000000.0
        ];
    }
}