<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Keuangan Manajemen (Modul E-2)</title>
    <!-- Menggunakan Tailwind CSS agar tampilannya langsung modern dan mirip mock-up -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <!-- Navbar Atas -->
    <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-900 text-white p-2 rounded-lg font-bold text-lg">🏢</div>
            <h1 class="text-xl font-bold text-slate-900">Dasbor Keuangan Manajemen</h1>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm font-medium bg-slate-100 px-3 py-1.5 rounded-full text-slate-700">
                👤 Budi Santoso <span class="text-slate-400">| Kepala Finance</span>
            </span>
            <a href="#" class="text-red-600 hover:text-red-800 text-sm font-semibold">Keluar</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- Filter Bar (UI-KE2-02) -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bulan</label>
                    <input type="text" value="October 2026" readonly class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm w-56 text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Proyek</label>
                    <input type="text" value="Semua proyek" readonly class="bg-slate-50 border border-slate-300 rounded-lg px-4 py-2 text-sm w-64 text-slate-700">
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:bg-slate-50">Reset</button>
                <button class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800">Terapkan</button>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Utama (Summary Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Kartu 1: Proyeksi Pemasukan -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Proyeksi Pemasukan</p>
                <p class="text-xs text-slate-500 mb-3">Estimasi kas masuk (RAB)</p>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Rp {{ $data['total_projected_income'] ?? '0' }}</h3>
                <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                    ↗ +4,2% vs target awal fiskal (Surplus)
                </span>
            </div>

            <!-- Kartu 2: Realisasi Pemasukan -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Realisasi Pemasukan</p>
                <p class="text-xs text-slate-500 mb-3">Penerimaan kas mutasi bank</p>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Rp {{ $data['net_cash_flow'] ?? '0' }}</h3>
                <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                    ✓ +2,5% Melampaui target (Surplus)
                </span>
            </div>

            <!-- Kartu 3: Selisih -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Selisih</p>
                <p class="text-xs text-slate-500 mb-3">Deviasi realisasi vs proyeksi</p>
                <h3 class="text-2xl font-bold text-emerald-600 mb-2">+Rp 370.500.000</h3>
                <span class="inline-block bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                    +2,49% Lebih tinggi dari proyeksi
                </span>
            </div>

            <!-- Kartu 4: Total Pengeluaran -->
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengeluaran</p>
                <p class="text-xs text-slate-500 mb-3">Disbursement & beban operasional</p>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">Rp {{ $data['total_tax_expense'] ?? '0' }}</h3>
                <span class="inline-block bg-slate-100 text-slate-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                    🛡️ 74,9% dari plafon anggaran (Terkendali)
                </span>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- WIDGET VISUALISASI (UI-KE2-03 & UI-KE2-04) -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- UI-KE2-03: Grafik Pemasukan (Proyeksi vs Realisasi) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Pemasukan: proyeksi vs realisasi</h3>
                        <p class="text-xs text-slate-500">Perbandingan arus kas proyeksi termin vs penerimaan bank per bulan</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <span class="flex items-center gap-1 text-slate-600"><span class="w-3 h-3 bg-blue-200 inline-block rounded-sm"></span> Proyeksi</span>
                        <span class="flex items-center gap-1 text-slate-600"><span class="w-3 h-3 bg-blue-900 inline-block rounded-sm"></span> Realisasi</span>
                    </div>
                </div>

                <!-- Simulasi Grafik Batang (Bar Chart Sederhana) -->
                <div class="h-48 flex items-end justify-between gap-2 border-b border-slate-200 pb-2 mb-4 px-2">
                    <!-- Mei -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-200 rounded-t-sm h-28" title="Proyeksi: 7.2M"></div>
                            <div class="w-3 bg-blue-900 rounded-t-sm h-32" title="Realisasi: 8.0M"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Mei</span>
                    </div>
                    <!-- Jun -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-200 rounded-t-sm h-30" title="Proyeksi"></div>
                            <div class="w-3 bg-blue-900 rounded-t-sm h-26" title="Realisasi"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Jun</span>
                    </div>
                    <!-- Jul -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-200 rounded-t-sm h-32" title="Proyeksi"></div>
                            <div class="w-3 bg-blue-900 rounded-t-sm h-32" title="Realisasi"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Jul</span>
                    </div>
                    <!-- Agu -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-200 rounded-t-sm h-34" title="Proyeksi"></div>
                            <div class="w-3 bg-blue-900 rounded-t-sm h-34" title="Realisasi"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Agu</span>
                    </div>
                    <!-- Sep -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-200 rounded-t-sm h-36" title="Proyeksi"></div>
                            <div class="w-3 bg-blue-900 rounded-t-sm h-38" title="Realisasi"></div>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Sep</span>
                    </div>
                    <!-- Okt (Highlighted Active) -->
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end bg-blue-50/50 p-1 rounded-lg border border-blue-100">
                        <div class="w-full flex items-end justify-center gap-1 h-36">
                            <div class="w-3 bg-blue-300 rounded-t-sm h-40" title="Proyeksi: 14.85B"></div>
                            <div class="w-3 bg-blue-950 rounded-t-sm h-44" title="Realisasi: 15.22B"></div>
                        </div>
                        <span class="text-xs text-blue-900 font-bold">Okt</span>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500 bg-slate-50 p-3 rounded-lg">
                    <span>💡 Klik batang atau angka untuk melihat rincian.</span>
                    <span class="font-semibold text-emerald-600">Surplus +Rp 370.500.000 (+2,4%)</span>
                </div>
            </div>

            <!-- UI-KE2-04: Breakdown Pengeluaran Bulanan -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900">Pengeluaran bulanan</h3>
                                <!-- Lencana status belum final sesuai spesifikasi UI-KE2-04 -->
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">● belum final</span>
                            </div>
                            <p class="text-xs text-slate-500">Periode: 2026-10</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Total Pengeluaran Bulan Ini</p>
                            <p class="text-lg font-bold text-slate-900">Rp 11.410.200.000</p>
                        </div>
                    </div>

                    <!-- Progress Components -->
                    <div class="space-y-4 my-4">
                        <!-- Payroll -->
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">PAYROLL (Gaji, Tunjangan, Bonus, BPJS, PPh 21)</span>
                                <span class="text-slate-900">Rp 6.840.000.000</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-900 h-full rounded-full" style="width: 60%"></div>
                            </div>
                        </div>

                        <!-- Pajak -->
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">Pajak (Ringkasan Pajak E-1)</span>
                                <span class="text-slate-900">Rp 2.120.000.000</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-700 h-full rounded-full" style="width: 20%"></div>
                            </div>
                        </div>

                        <!-- Operasional -->
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">Operasional (Listrik, Internet, Petty Cash)</span>
                                <span class="text-slate-900">Rp 2.450.200.000</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full" style="width: 20%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs text-slate-500 bg-slate-50 p-3 rounded-lg border border-slate-100">
                    <span>⚙️ Data teragregasi per komponen</span>
                    <a href="{{ url('/dashboard-eksekutif/rincian') }}" class="text-blue-600 font-semibold hover:underline">Kelola / Rincian →</a>
                </div>
            </div>

        </div>

        <!-- Tabel Ringkasan Proyek dan Invoice -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        Ringkasan Proyek dan Invoice 
                        <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full">5 Tagihan Aktif</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Monitoring penerimaan tagihan per termin berjalan per 31 Oktober 2026</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-bold uppercase">Akumulasi Nilai Invoice</p>
                    <p class="text-base font-bold text-slate-900">Rp 15.220.500.000</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="py-3 px-6 font-semibold">Proyek</th>
                            <th class="py-3 px-6 font-semibold">Klien</th>
                            <th class="py-3 px-6 font-semibold">Nilai Invoice</th>
                            <th class="py-3 px-6 font-semibold">Status</th>
                            <th class="py-3 px-6 font-semibold text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        <tr>
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-900">Proyek Tol Sumatera Tahap 2</p>
                                <span class="text-xs text-slate-400">TERMIN-III/TS2/2026-09 · Konstruksi Fisik</span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-slate-900">PT Hutama Karya (Persero)</p>
                                <span class="text-xs text-slate-400">BUMN Infrastruktur</span>
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                Rp 5.850.000.000
                                <span class="block text-xs font-normal text-slate-400">PPN 11% Tercakup</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-1 rounded-full">✓ Terbayar</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ url('/dashboard-eksekutif/rincian') }}" class="text-blue-600 font-medium hover:underline">Rincian →</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-900">Infrastruktur Cloud Datacenter</p>
                                <span class="text-xs text-slate-400">TERMIN-II/KOM/2026-10 · Pengadaan Server Tier 4</span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-slate-900">Kementerian Kominfo</p>
                                <span class="text-xs text-slate-400">Lembaga Pemerintah</span>
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                Rp 4.220.500.000
                                <span class="block text-xs font-normal text-slate-400">PPh Pasal 22 Terpotong</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-1 rounded-full">✓ Terbayar</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ url('/dashboard-eksekutif/rincian') }}" class="text-blue-600 font-medium hover:underline">Rincian →</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6">
                                <p class="font-bold text-slate-900">Revitalisasi Kantor Cabang Surabaya</p>
                                <span class="text-xs text-slate-400">TERMIN-FINAL/BM-SBY/2026 · Serah Terima Akhir</span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-semibold text-slate-900">Bank Mandiri (Persero) Tbk</p>
                                <span class="text-xs text-slate-400">BUMN Perbankan</span>
                            </td>
                            <td class="py-4 px-6 font-semibold">
                                Rp 2.150.000.000
                                <span class="block text-xs font-normal text-slate-400">Retensi 5% Diterbitkan</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-1 rounded-full">⌛ Tertagih</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ url('/dashboard-eksekutif/rincian') }}" class="text-blue-600 font-medium hover:underline">Rincian →</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Footer Tabel -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs text-slate-500">
                <span>🔒 Semua dokumen penagihan divalidasi dengan e-Faktur DJP dan sistem perbankan host-to-host.</span>
                <span>Sinkronisasi Terakhir: 15 Menit Lalu</span>
            </div>
        </div>

    </main>

</body>
</html>