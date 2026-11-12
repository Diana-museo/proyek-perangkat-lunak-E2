@extends('layouts.app')

@section('title', 'Pengeluaran - Modul E-2')
@section('header-title', 'Rincian Beban & Pengeluaran Perusahaan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @php
        $erp = new \App\Services\ErpIntegrationService(session('token'));
        $taxData = $erp->getTaxSummary('2026-10');
        $payrollData = $erp->getPayrollRekap('01a1193d-2500-7c04-854b-8bf03c711dde');
        
        // Perhitungan Komponen Pajak E-1
        $totalPpn = floatval($taxData['totalPpn'] ?? 0);
        $totalPpnbm = floatval($taxData['totalPpnbm'] ?? 0);
        $taxExpense = $totalPpn + $totalPpnbm;

        // Perhitungan Komponen Payroll F-5
        $gajiBersih = floatval($payrollData['totalGajiBersih'] ?? 0);
        $bpjsPrs = floatval($payrollData['totalBpjsPerusahaan'] ?? 0);
        $payrollExpense = $gajiBersih + $bpjsPrs;
        
        // Total Pengeluaran Keseluruhan
        $totalPengeluaran = $taxExpense + $payrollExpense;

        // Persentase Komposisi untuk Stacked Progress Bar
        $payrollPct = $totalPengeluaran > 0 ? round(($payrollExpense / $totalPengeluaran) * 100, 1) : 0;
        $taxPct = $totalPengeluaran > 0 ? round(($taxExpense / $totalPengeluaran) * 100, 1) : 0;
    @endphp

    <!-- Card Ringkasan Utama & Visualisasi Komposisi Donut/Progress Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-6">
        <div class="flex flex-wrap justify-between items-start border-b border-slate-100 pb-4 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">Pengeluaran Bulanan Terintegrasi</h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">Periode Pajak: {{ $taxData['taxPeriod'] ?? '2026-10' }} · Payroll: {{ $payrollData['namaPeriode'] ?? 'Oktober 2026' }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 font-bold uppercase">Total Pengeluaran Keseluruhan</p>
                <p class="text-2xl font-black text-slate-900">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Visualisasi Stacked Progress Bar Komposisi Pengeluaran -->
        <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
            <div class="flex justify-between items-center text-xs font-bold text-slate-700">
                <span>Komposisi Alokasi Pengeluaran</span>
                <span class="text-slate-500 font-semibold">Payroll: <strong class="text-blue-900">{{ $payrollPct }}%</strong> | Pajak: <strong class="text-purple-900">{{ $taxPct }}%</strong></span>
            </div>
            
            <!-- Bar Stacked -->
            <div class="w-full bg-slate-200 h-3.5 rounded-full overflow-hidden flex shadow-inner">
                <div class="bg-blue-900 h-full transition-all duration-500" style="width: {{ $payrollPct }}%" title="Beban Payroll: {{ $payrollPct }}%"></div>
                <div class="bg-purple-700 h-full transition-all duration-500" style="width: {{ $taxPct }}%" title="Beban Pajak: {{ $taxPct }}%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-blue-900 rounded-sm inline-block"></span> Total Payroll: Rp {{ number_format($payrollExpense, 0, ',', '.') }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-purple-700 rounded-sm inline-block"></span> Total Pajak: Rp {{ number_format($taxExpense, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Struktur Card Pengeluaran & Grid Kelengkapan F-5 & E-1 Berdampingan (Side-by-Side) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kolom 1: Rincian Payroll Lengkap (Modul F-5) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">■ PAYROLL (Modul F-5)</h3>
                        <p class="text-[11px] text-slate-500">Rekapitulasi Gaji & BPJS Pegawai</p>
                    </div>
                    <span class="text-base font-extrabold text-blue-900">Rp {{ number_format($payrollExpense, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Nama Periode Gaji</span>
                        <span class="font-semibold text-slate-900">{{ $payrollData['namaPeriode'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Status Siklus Periode</span>
                        <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px]">{{ $payrollData['statusPeriode'] ?? 'Disetujui' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Jumlah Total Pegawai</span>
                        <span class="font-semibold text-slate-900">{{ $payrollData['jumlahKaryawan'] ?? 0 }} orang</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Total Pendapatan Kotor</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format(floatval($payrollData['totalPendapatan'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Rincian Total Bonus</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format(floatval($payrollData['totalBonus'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Total Potongan Komponen</span>
                        <span class="font-semibold text-red-600">Rp {{ number_format(floatval($payrollData['totalPotongan'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Potongan BPJS Karyawan</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format(floatval($payrollData['totalBpjsKaryawan'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50 bg-slate-50/80 px-2 rounded">
                        <span class="font-bold text-slate-700">Total Gaji Bersih Dibayarkan</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 px-2 rounded bg-blue-50/50">
                        <span class="font-bold text-blue-900">Beban BPJS Perusahaan</span>
                        <span class="font-bold text-blue-900">Rp {{ number_format($bpjsPrs, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 2: Rincian Pajak & Status Kepatuhan (Modul E-1) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">■ PAJAK (Modul E-1)</h3>
                        <p class="text-[11px] text-slate-500">Ringkasan PPN, PPnBM & Status Setoran</p>
                    </div>
                    <span class="text-base font-extrabold text-purple-900">Rp {{ number_format($taxExpense, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Masa Pajak / Periode</span>
                        <span class="font-semibold text-slate-900">{{ $taxData['taxPeriod'] ?? '2026-10' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Jumlah Invoice Terhitung</span>
                        <span class="font-semibold text-slate-900">{{ $taxData['invoiceCount'] ?? 0 }} invoice</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Akumulasi Total PPN</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($totalPpn, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600">Akumulasi Total PPnBM</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($totalPpnbm, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2 px-3 rounded bg-slate-50 items-center">
                        <span class="font-bold text-slate-700">Status Pembayaran</span>
                        @php
                            $status = strtoupper($taxData['paymentStatus'] ?? 'CONFIRMED');
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $status === 'CONFIRMED' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection