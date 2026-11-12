<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'emailOrUsername' => 'required',
            'password' => 'required',
        ]);

        $url = "http://127.0.0.1:4100/auth/login";

        try {
            $response = Http::timeout(5)->post($url, [
                'emailOrUsername' => $request->emailOrUsername,
                'password' => $request->password,
            ]);

            $data = $response->json();

            // Jika login berhasil (HTTP 200)
            if ($response->successful()) {
                $accessToken = $data['accessToken'] ?? null;
                $roleCode = $data['account']['roleCode'] ?? ($data['roleCode'] ?? null);
                $name = $data['account']['name'] ?? 'Pengguna';

                // Validasi hak akses khusus Modul E-2
                $allowedRoles = ['CEO', 'COO', 'CFO', 'ADMIN', 'KEPALA_FINANCE']; 

                if ($roleCode && !in_array($roleCode, $allowedRoles)) {
                    // Panggil view forbidden-access dengan benar dan berikan status 403
                    return view('auth.forbidden-access'); 
                }

                session([
                    'token' => $accessToken,
                    'userRole' => $roleCode,
                    'userName' => $name,
                ]);

                return redirect('/dashboard')->with('success', "Login Berhasil! Selamat datang {$name}");
            }

            // ===================================================================
            // AMBIL PESAN LANGSUNG DARI SERVER F-1 (Status 401, 403, dll.)
            // ===================================================================
            // Server F-1 selalu mengirim key "message", misal: $data['message']
            $f1ErrorMessage = $data['message'] ?? 'Terjadi kesalahan pada server autentikasi.';

            return back()->withErrors([
                'login_error' => $f1ErrorMessage
            ])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors([
                'login_error' => 'Gagal terhubung ke Server F-1: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/login');
    }
}