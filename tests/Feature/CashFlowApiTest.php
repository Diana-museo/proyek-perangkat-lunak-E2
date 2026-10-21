<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashFlowApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_cashflow_summary()
    {
        $response = $this->getJson('/api/v1/cashflow/summary');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'code' => 200,
                     'message' => 'Ringkasan arus kas modul E-2'
                 ])
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'data' => [
                         'total_income',
                         'total_expense',
                         'net_cash_flow',
                         'currency'
                     ]
                 ]);
    }

    /** @test */
    public function it_can_fetch_aggregated_dashboard_summary()
    {
        $response = $this->getJson('/api/v1/dashboard/aggregated-summary');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'code' => 200
                 ])
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'data' => [
                         'project_info' => [
                             'projectId',
                             'canonicalCode',
                             'projectName',
                             'clientId',
                             'clientName',
                             'poReceived',
                             'status'
                         ],
                         'projected_income_from_d2',
                         'tax_expense_from_e1',
                         'payroll_expense_from_f5',
                         'net_cash_flow',
                         'financial_health',
                         'currency'
                     ]
                 ]);
    }

    /** @test */
    public function it_can_store_pemasukan_luar_jadwal()
    {
        $payload = [
            'nominal' => 2500000.00,
            'tanggalTransaksi' => '2026-10-16',
            'keterangan' => 'Uji coba pemasukan manual'
        ];

        $response = $this->postJson('/api/v1/pemasukan-luar-jadwal', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'code' => 201,
                     'message' => 'Data pemasukan luar jadwal berhasil disimpan'
                 ]);
    }
}
