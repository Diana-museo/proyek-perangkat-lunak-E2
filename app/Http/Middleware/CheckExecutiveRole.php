<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExecutiveRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah token sudah ada di Session Laravel
        $token = session('token');

        if (!$token) {
            return redirect('/login')->withErrors(['login_error' => 'Sesi habis atau token tidak ditemukan, silakan login terlebih dahulu.']);
        }

        // Ambil role langsung dari session yang disimpan saat login sukses
        $userRole = strtoupper(session('userRole') ?? '');
        
        // Daftar role yang diizinkan mengakses E-2 (Eksekutif)
        $allowedRoles = ['CEO', 'COO', 'CFO', 'KEPALA_FINANCE'];

        if (!in_array($userRole, $allowedRoles)) {
            return response()->view('auth.forbidden-access', [], 403);
        }

        return $next($request);
    }
}