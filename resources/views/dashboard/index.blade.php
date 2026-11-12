@extends('layouts.app')

@section('title', 'Dasbor Utama')
@section('header-title', 'Dasbor Keuangan Manajemen')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Filter Bar Kalender Bulan & Tahun -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <form method="GET" action="{{ url('/dashboard') }}" class="flex flex-wrap gap-4 items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Periode Bulan & Tahun</label>
                    <input type="month" name="month" value="{{ $selectedMonth ?? '2026-10' }}" class="bg-white border border-slate-300 rounded-lg px-4 py-2 text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-900 shadow-sm">
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="bg-blue-900 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-800 transition">
                    Terapkan Filter
                </button>
                <a href="{{ url('/dashboard') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Informasi Filter Aktif -->
    <div class="text-xs text-slate-500 bg-slate-100 px-4 py-2 rounded-lg font-medium inline-block">
        Filter Aktif Bulan: <span class="font-bold text-slate-700">{{ $selectedMonth ?? '2026-10' }}</span>
    </div>

    <!-- 4 Kartu KPI Utama -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proyeksi Pemasukan</p>
            <p class="text-xs text-slate-500 mb-3">Target pembayaran termin</p>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Rp {{ number_format(floatval($data['total_projected_income'] ?? 0), 0, ',', '.') }}</h3>
            <span class="inline-block bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                {{ count($termins ?? []) }} termin aktif
            </span>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Invoice Tertagih</p>
            <p class="text-xs text-slate-500 mb-3">Tagihan aktif berstatus tertagih</p>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Rp {{ number_format(floatval($data['total_invoice_tertagih'] ?? 0), 0, ',', '.') }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengeluaran</p>
            <p class="text-xs text-slate-500 mb-3">Payroll + Pajak PPN</p>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">
                Rp {{ number_format(floatval($data['total_pengeluaran'] ?? 0), 0, ',', '.') }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Net Cash Flow</p>
            <p class="text-xs text-slate-500 mb-3">Pemasukan bersih</p>
            <h3 class="text-2xl font-bold {{ ($data['net_cash_flow'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }} mb-2">
                Rp {{ number_format(floatval($data['net_cash_flow'] ?? 0), 0, ',', '.') }}
            </h3>
            <span class="inline-block {{ ($data['net_cash_flow'] ?? 0) >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} text-xs font-semibold px-2.5 py-1 rounded-md">
                {{ ($data['net_cash_flow'] ?? 0) >= 0 ? '✓ Surplus Kas' : '⚠️ Defisit' }}
            </span>
        </div>
    </div>

    <!-- Progress Bar Rasio Alokasi Beban (Bahasa Profesional) -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-3">
        <div class="flex justify-between items-center text-xs font-bold uppercase tracking-wider text-slate-600">
            <span>Rasio Alokasi Beban Pengeluaran terhadap Pemasukan</span>
            <span class="text-blue-900 font-extrabold text-sm">{{ $data['expense_ratio'] ?? 0 }}% teralokasi</span>
        </div>
        
        <div class="w-full bg-slate-100 h-4 rounded-full overflow-hidden p-0.5 border border-slate-200">
            <div class="bg-blue-900 h-full rounded-full transition-all duration-500" style="width: {{ $data['expense_ratio'] ?? 0 }}%;"></div>
        </div>

        <div class="flex justify-between items-center text-[11px] text-slate-500 pt-1">
            <span>💡 Indikator proporsi penyerapan pendapatan bulanan untuk menutupi kewajiban operasional dan pajak.</span>
            <span>Total Pengeluaran: Rp {{ number_format($data['total_pengeluaran'], 0, ',', '.') }} dari Pemasukan Rp {{ number_format($data['total_projected_income'], 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Tabel Ringkasan Proyek dan Invoice (Data dari Server Hulu D-1 & D-2) -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    Ringkasan Proyek dan Invoice 
                    <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full">{{ count($termins ?? []) }} termin</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Identitas proyekdan data termin untuk periode {{ $selectedMonth }}.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase tracking-wider">
                        <th class="py-3 px-6 font-semibold">Proyek</th>
                        <th class="py-3 px-6 font-semibold">Klien / Kontrak</th>
                        <th class="py-3 px-6 font-semibold">Termin / Invoice</th>
                        <th class="py-3 px-6 font-semibold">Nilai Termin</th>
                        <th class="py-3 px-6 font-semibold">Status</th>
                        <th class="py-3 px-6 font-semibold text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                    @forelse($termins as $termin)
                    <tr>
                        <td class="py-4 px-6 font-bold text-slate-900">
                            {{ $termin['projectName'] ?? '-' }}<br>
                            <span class="font-mono font-normal text-[10px] text-slate-400">{{ $termin['projectId'] ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6 text-slate-600">
                            {{ $termin['clientName'] ?? '-' }}<br>
                            <span class="text-[10px] text-slate-400">Kontrak: {{ $termin['contractNumber'] ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6 font-mono">
                            {{ $termin['terminId'] ?? '-' }}<br>
                            <span class="text-[10px] text-slate-400 font-sans">Jatuh Tempo: {{ $termin['dueDate'] ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            Rp {{ number_format(floatval($termin['terminValue'] ?? 0), 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="bg-emerald-100 text-emerald-800 text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $termin['status'] ?? '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <a href="{{ url('/dashboard-eksekutif/rincian/' . ($termin['projectId'] ?? '')) }}" class="text-blue-600 font-medium hover:underline">
                                Rincian →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500 font-medium bg-slate-50/50">
                            ⚠️ Belum ada data proyek atau termin untuk bulan dan tahun ini ({{ $selectedMonth }}).
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection