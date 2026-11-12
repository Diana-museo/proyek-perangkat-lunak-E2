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

    public function verifyRoleAndToken($token)
    {
        $baseUrl = env('F1_SERVICE_URL', 'http://127.0.0.1:4010');

        try {
            $response = Http::withToken($token)
                ->timeout(2)
                ->get("{$baseUrl}/auth/verify");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::warning("Gagal terhubung ke F-1: " . $e->getMessage());
        }

        return null;
    }

    public function checkUpstreamStatus()
    {
        $statusReport = [
            'd1' => false,
            'd2' => false,
            'e1' => false,
            'f5' => false,
        ];

        // Cek D-1
        try {
            $res = Http::withToken($this->token)->timeout(1)->get(env('D1_SERVICE_URL', 'http://localhost:3000/api/v1') . '/projects');
            if ($res->successful()) $statusReport['d1'] = true;
        } catch (\Exception $e) {}

        // Cek D-2
        try {
            $res = Http::withToken($this->token)->timeout(1)->get(env('D2_SERVICE_URL', 'http://localhost:8000') . '/api/v1/termins');
            if ($res->successful()) $statusReport['d2'] = true;
        } catch (\Exception $e) {}

        // Cek E-1
        try {
            $res = Http::withToken($this->token)->timeout(1)->get(env('E1_SERVICE_URL', 'http://127.0.0.1:8000') . '/api/v1/tax/summary?taxPeriod=2026-10');
            if ($res->successful()) $statusReport['e1'] = true;
        } catch (\Exception $e) {}

        // Cek F-5
        try {
            $res = Http::withToken($this->token)->timeout(1)->get(env('F5_SERVICE_URL', 'http://127.0.0.1:8000') . '/api/payroll/v1/rekap-biaya?periodeId=01a1193d-2500-7c04-854b-8bf03c711dde');
            if ($res->successful()) $statusReport['f5'] = true;
        } catch (\Exception $e) {}

        return $statusReport;
    }

    // 1. Modul D-1 (Registrasi Proyek)
    public function getProject($projectId)
    {
        $projectId = $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567";
        $baseUrl = env('D1_SERVICE_URL', 'http://localhost:3000/api/v1');
        
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

        // FALLBACK: Mock Data D-1 (Single Project)
        return [
            "projectId" => $projectId,
            "canonicalCode" => "202601",
            "projectName" => "Modernisasi Gedung Operasional",
            "clientId" => "CL-001",
            "clientName" => "PT Cakra Infrastruktur",
            "amEmployeeId" => "01929b4a-6e2a-7b3f-8c71-0ab4e3d9f2a1",
            "amName" => "Fikri Lazuardi Fadilla (AM)",
            "pmEmployeeId" => "01929b4a-5f10-7a22-9d44-3c8f1b2e7a90",
            "pmName" => "Sandy Walsh",
            "startDate" => "2026-03-01",
            "targetEndDate" => "2026-08-31",
            "documentsComplete" => true,
            "poReceived" => true,
            "status" => "REGISTERED",
            "active" => true
        ];
    }

    public function getProjects($params = [])
    {
        $baseUrl = env('D1_SERVICE_URL', 'http://localhost:3000/api/v1');

        try {
            $response = Http::withToken($this->token)
                ->timeout(3)
                ->get("{$baseUrl}/projects", $params);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json('items') ?? $response->json();
                // Pastikan selalu mengembalikan array
                return is_array($data) ? $data : [];
            }

            Log::warning("D-1 Live Server (List Projects) mengembalikan status non-success: " . $response->status());
        } catch (\Exception $e) {
            Log::warning("Gagal koneksi ke D-1 Live Server untuk getProjects, beralih ke Mock. Error: " . $e->getMessage());
        }

        // FALLBACK: Mock Daftar Proyek D-1
        return [
            [
                "projectId" => "01924b1e-7f32-7a81-9b12-45e8a1234567",
                "canonicalCode" => "202601",
                "projectName" => "Modernisasi Gedung Operasional",
                "clientId" => "CL-001",
                "clientName" => "PT Cakra Infrastruktur",
                "amEmployeeId" => "01929b4a-6e2a-7b3f-8c71-0ab4e3d9f2a1",
                "amName" => "Fikri Lazuardi Fadilla (AM)",
                "pmEmployeeId" => "01929b4a-5f10-7a22-9d44-3c8f1b2e7a90",
                "pmName" => "Sandy Walsh",
                "startDate" => "2026-03-01",
                "targetEndDate" => "2026-08-31",
                "documentsComplete" => true,
                "poReceived" => true,
                "status" => "REGISTERED",
                "active" => true,
                "createdAt" => "2026-02-15T08:30:00+07:00",
                "updatedAt" => "2026-02-18T10:00:00+07:00"
            ],
            [
                "projectId" => "prj-demo-002",
                "canonicalCode" => "202602",
                "projectName" => "Pengembangan Infrastruktur Jaringan Cloud",
                "clientId" => "CL-002",
                "clientName" => "Kementerian Kominfo",
                "amEmployeeId" => "01929b4a-6e2a-7b3f-8c71-0ab4e3d9f2a2",
                "amName" => "Rina Melati",
                "pmEmployeeId" => "01929b4a-5f10-7a22-9d44-3c8f1b2e7a91",
                "pmName" => "Budi Santoso",
                "startDate" => "2026-04-01",
                "targetEndDate" => "2026-10-31",
                "documentsComplete" => false,
                "poReceived" => false,
                "status" => "DRAFT",
                "active" => false,
                "createdAt" => "2026-03-01T08:30:00+07:00",
                "updatedAt" => "2026-03-01T08:30:00+07:00"
            ]
        ];
    }

    // 2. Modul D-2 (Kontrak & Termin)
    public function getTermins($projectId = null)
    {
        $baseUrl = env('D2_SERVICE_URL', 'http://127.0.0.1:8000');

        try {
            $response = Http::withToken($this->token)
                ->timeout(3)
                ->get("{$baseUrl}/api/v1/termins", [
                    'projectId' => $projectId
                ]);

            if ($response->successful()) {
                $data = $response->json('data.items') ?? $response->json('data') ?? $response->json();
                return is_array($data) ? $data : [];
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
                "contractId" => "ktr-demo-001",
                "contractNumber" => "KTR/2026/001",
                "projectId" => $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567",
                "poStatus" => "PO_ADA"
            ],
            [
                "terminId" => "trm-demo-002",
                "terminNumber" => 2,
                "terminValue" => "150000000.00",
                "dueDate" => "2026-12-15",
                "status" => "SIAP_DITAGIH",
                "contractId" => "ktr-demo-001",
                "contractNumber" => "KTR/2026/001",
                "projectId" => $projectId ?? "01924b1e-7f32-7a81-9b12-45e8a1234567",
                "poStatus" => "PO_ADA"
            ]
        ];
    }

    // 3. Modul E-1 (Pajak)
    public function getTaxSummary($taxPeriod, $projectId = null)
    {
        $baseUrl = env('E1_SERVICE_URL', 'http://127.0.0.1:8000');

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
            "periodeId" => $periodeId,
            "namaPeriode" => "Oktober 2026",
            "tanggalMulai" => "2026-10-01",
            "tanggalAkhir" => "2026-10-31",
            "statusPeriode" => "Disetujui",
            "adaData" => true,
            "jumlahKaryawan" => 1,
            "totalPendapatan" => 7750000.0,
            "totalBonus" => 750000.0,
            "totalPotongan" => 200000.0,
            "totalBpjsKaryawan" => 65000.0,
            "totalGajiBersih" => 7485000.0,
            "totalBpjsPerusahaan" => 260000.0,
            "totalTermin" => 25000000.0,
            "jumlahTermin" => 1,
            "totalBiaya" => null,
            "diperbaruiPada" => "2026-10-13T19:43:00+07:00"
        ];
    }
}