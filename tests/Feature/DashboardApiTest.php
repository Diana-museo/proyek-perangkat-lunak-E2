<?php

namespace Tests\Feature;

use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    /** @test */
    public function it_returns_unauthorized_if_token_is_missing()
    {
        $response = $this->getJson('/api/v1/dashboard/aggregated-summary');

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 'ERR-AUTH-401',
                 ]);
    }

    /** @test */
    public function it_returns_unauthorized_if_token_is_invalid()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                         ->getJson('/api/v1/dashboard/aggregated-summary');

        // Disesuaikan dengan middleware baru (token salah/gagal diverifikasi ke F-1 bernilai 401)
        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 'ERR-AUTH-401',
                 ]);
    }

    /** @test */
    public function it_can_fetch_aggregated_summary_successfully()
    {
        // Catatan: Saat uji coba live, pastikan token ini valid di server F-1 
        $response = $this->withHeader('Authorization', 'Bearer secret-token-valid')
                         ->getJson('/api/v1/dashboard/aggregated-summary');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'code' => 'E2-200',
                 ])
                 ->assertJsonStructure([
                     'status',
                     'code',
                     'message',
                     'data' => [
                         'total_projected_income',
                         'total_tax_expense',
                         'total_payroll_expense',
                         'net_cash_flow',
                         'currency',
                         'breakdown_sources'
                     ]
                 ]);
    }

    /** @test */
    public function it_validates_monthly_trend_query_parameters()
    {
        $response = $this->withHeader('Authorization', 'Bearer secret-token-valid')
                         ->getJson('/api/v1/cashflow/monthly?year=salah');

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 'ERR-REQ-400',
                 ]);
    }
}