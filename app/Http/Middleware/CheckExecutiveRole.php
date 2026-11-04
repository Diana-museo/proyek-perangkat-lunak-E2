<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class CheckExecutiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil token dari Header (Authorization: Bearer <token>) atau Query Parameter ?token=
        $header = $request->header('Authorization');
        $token = null;

        if ($header && str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        } else {
            $token = $request->query('token');
        }

        // Jika tidak ada token sama sekali
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-AUTH-401',
                'message' => 'Token akses tidak ditemukan.'
            ], 401);
        }

        // 2. Ambil URL F-1 dari .env
        $baseUrl = env('F1_SERVICE_URL', 'http://127.0.0.1:4010');

        try {
            // 3. Tembak HTTP Request nyata ke Prism F-1 temanmu di jaringan Wi-Fi
            $response = Http::withToken($token)
                ->timeout(3)
                ->get("{$baseUrl}/accounts/current"); // Endpoint F-1

            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'ERR-AUTH-401',
                    'message' => 'Token tidak valid atau kedaluwarsa di F-1.'
                ], 401);
            }

            // 4. Cek Role dari respon F-1 (CEO, COO, CFO)
            $userData = $response->json();
            $userRole = strtoupper($userData['role'] ?? '');
            $allowedRoles = ['CEO', 'COO', 'CFO'];

            if (!in_array($userRole, $allowedRoles)) {
                // Tampilkan halaman 403 Forbidden jika role tidak berwenang
                return response()->view('dashboard.forbidden', [], 403);
            }

            // Simpan data identitas ke request agar bisa dipakai Controller
            $request->attributes->set('user_identity', $userData);

        } catch (\Exception $e) {
            // Jika Prism F-1 offline / gagal diakses
            return response()->json([
                'status' => 'error',
                'code' => 'ERR-DS-503',
                'message' => 'Gagal terhubung ke layanan F-1: ' . $e->getMessage()
            ], 503);
        }

        return $next($request);
    }
}