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
                     'message' => 'Token akses tidak valid atau kedaluwarsa.'
                 ]);
    }

    /** @test */
    public function it_returns_forbidden_if_token_is_invalid()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                         ->getJson('/api/v1/dashboard/aggregated-summary');

        $response->assertStatus(403)
                 ->assertJson([
                     'status' => 'error',
                     'code' => 'ERR-AUTH-403',
                     'message' => 'Akun pengguna tidak memiliki hak akses tingkat eksekutif.'
                 ]);
    }

    /** @test */
    public function it_can_fetch_aggregated_summary_successfully()
    {
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