<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
        
        try {
            // Coba ambil secara live (timeout 2 detik agar tidak lama menunggu)
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("http://127.0.0.1:4010/projects/{$projectId}");

            if ($response->successful()) {
                return $response->json('data') ?? $response->json();
            }
        } catch (\Exception $e) {
            // Server live mati/tidak aktif, abaikan dan lanjut ke mock
        }

        // FALLBACK: Mock Data D-1
        return [
            "projectId" => $projectId,
            "canonicalCode" => "202601",
            "projectName" => "Implementasi Sistem ERP Cloud Anaheim Tahap 1 [Live Offline - Mock Mode]",
            "clientId" => "PTANTRI",
            "clientName" => "PT Anaheim Nimbus Universal",
            "poReceived" => true,
            "status" => "REGISTERED"
        ];
    }

    // 2. Modul D-2 (Kontrak & Termin)
    public function getTermins($projectId = null)
    {
        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("http://localhost:8000/api/v1/termins", [
                    'projectId' => $projectId
                ]);

            if ($response->successful()) {
                return $response->json('data.items') ?? [];
            }
        } catch (\Exception $e) {
            // Server live mati, pakai mock
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
        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("http://localhost:8080/api/v1/tax/summary", [
                    'taxPeriod' => $taxPeriod,
                    'projectId' => $projectId
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? $response->json();
            }
        } catch (\Exception $e) {
            // Server live mati, pakai mock
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
        try {
            $response = Http::withToken($this->token)
                ->timeout(2)
                ->get("http://127.0.0.1:8000/api/payroll/v1/rekap-biaya", [
                    'periodeId' => $periodeId
                ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Server live mati, pakai mock
        }

        // FALLBACK: Mock Data F-5
        return [
            "periodeId" => $periodeId ?? "01a1193d-2500-7c04-854b-8bf03c711dde",
            "namaPeriode" => "Oktober 2026",
            "jumlahKaryawan" => 5,
            "totalGajiBersih" => 37425000.0,
            "totalTermin" => 25000000.0
        ];
    }
}