<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Pemasukan - Modul E-2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900/50 font-sans text-slate-800 flex items-center justify-center min-h-screen p-6">

    <!-- Modal / Container Rincian Drill-Down (UI-KE2-05) -->
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl overflow-hidden border border-slate-200">
        
        <!-- Header Modal -->
        <div class="px-8 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Dasbor > Pemasukan > Proyek > Bulan</p>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">Rincian pemasukan</h2>
            </div>
            <a href="{{ url('/dashboard-eksekutif') }}" class="text-slate-400 hover:text-slate-600 font-semibold text-sm">
                ✕ Tutup
            </a>
        </div>

        <!-- Navigasi Tab Sumber (UI-KE2-05) -->
        <div class="flex border-b border-slate-200 px-8 bg-slate-50/50 gap-8">
            <button class="py-3 text-sm font-bold text-blue-900 border-b-2 border-blue-900">Invoice</button>
            <button class="py-3 text-sm font-medium text-slate-500 hover:text-slate-800">Penerimaan bank</button>
            <button class="py-3 text-sm font-medium text-slate-500 hover:text-slate-800">Payroll</button>
            <button class="py-3 text-sm font-medium text-slate-500 hover:text-slate-800">Pajak</button>
        </div>

        <!-- Konten Tabel Rincian -->
        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">Invoice</th>
                            <th class="py-3 px-4 font-semibold">Tanggal</th>
                            <th class="py-3 px-4 font-semibold">Termin</th>
                            <th class="py-3 px-4 font-semibold text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <tr>
                            <td class="py-4 px-4 font-medium text-slate-900">INV-2026-001</td>
                            <td class="py-4 px-4 text-slate-500">2026-10-04</td>
                            <td class="py-4 px-4 text-slate-600">Termin 1 (Uang Muka)</td>
                            <td class="py-4 px-4 text-right font-semibold">Rp 4.500.000.000</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 font-medium text-slate-900">INV-2026-002</td>
                            <td class="py-4 px-4 text-slate-500">2026-10-12</td>
                            <td class="py-4 px-4 text-slate-600">Termin 2 (Konstruksi 30%)</td>
                            <td class="py-4 px-4 text-right font-semibold">Rp 5.200.000.000</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 font-medium text-slate-900">INV-2026-003</td>
                            <td class="py-4 px-4 text-slate-500">2026-10-19</td>
                            <td class="py-4 px-4 text-slate-600">Termin 3 (Material Pasokan)</td>
                            <td class="py-4 px-4 text-right font-semibold">Rp 3.120.500.000</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 font-medium text-slate-900">INV-2026-004</td>
                            <td class="py-4 px-4 text-slate-500">2026-10-27</td>
                            <td class="py-4 px-4 text-slate-600">Termin 4 (Instalasi Sistem)</td>
                            <td class="py-4 px-4 text-right font-semibold">Rp 2.400.000.000</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 font-bold text-slate-900">
                            <td colspan="3" class="py-4 px-4">Total rincian</td>
                            <td class="py-4 px-4 text-right">Rp 15.220.500.000</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer Modal dengan Indikator Kecocokan -->
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <a href="{{ url('/') }}" class="px-5 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                Kembali ke dasbor
            </a>
            
            <!-- Indikator validasi kecocokan total angka sesuai spesifikasi UI-KE2-05 -->
            <div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg text-xs font-semibold">
                <span>✓ Cocok dengan angka dasbor</span>
            </div>
        </div>

    </div>

</body>
</html>