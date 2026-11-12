@extends('layouts.app')

@section('title', 'Manajemen Proyek - Modul D-1')
@section('header-title', 'Daftar Proyek Kanonis & Validasi Purchase Order')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    @if(isset($isSyncFailed) && $isSyncFailed)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Data proyek belum tersedia</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Gagal mengambil data dari server hulu Modul D-1.</p>
            <button onclick="window.location.reload();" class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Muat ulang halaman
            </button>
        </div>
    @else
        <!-- Kartu Statistik Singkat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total Proyek Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-900">{{ $totalProyekAktif }} Proyek</h3>
                </div>
                <div class="p-3 bg-blue-50 text-blue-800 rounded-xl font-bold text-lg">🏢</div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proyek Tertahan (Blokir Penagihan)</p>
                    <h3 class="text-2xl font-bold {{ $proyekTertahan > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $proyekTertahan }} Proyek</h3>
                </div>
                <div class="p-3 {{ $proyekTertahan > 0 ? 'bg-red-50 text-red-800' : 'bg-emerald-50 text-emerald-800' }} rounded-xl font-bold text-lg">⚠️</div>
            </div>
        </div>

        <!-- Filter Cepat & Tabel Induk Proyek -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Daftar Induk Proyek Kanonis</h3>
                </div>

                <!-- Filter Cepat Status Siklus -->
                <form method="GET" action="{{ url('/dashboard/proyek') }}" class="flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-600">Filter Status:</label>
                    <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-blue-900">
                        <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                        <option value="REGISTERED" {{ $statusFilter === 'REGISTERED' ? 'selected' : '' }}>REGISTERED</option>
                        <option value="DRAFT" {{ $statusFilter === 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                        <option value="REVISI_HANDOVER" {{ $statusFilter === 'REVISI_HANDOVER' ? 'selected' : '' }}>REVISI_HANDOVER</option>
                        <option value="UNDER_REVIEW" {{ $statusFilter === 'UNDER_REVIEW' ? 'selected' : '' }}>UNDER_REVIEW</option>
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase tracking-wider">
                            <th class="py-3 px-6 font-semibold">Kode & Nama Proyek</th>
                            <th class="py-3 px-6 font-semibold">Klien</th>
                            <th class="py-3 px-6 font-semibold">Penanggung Jawab (AM / PM)</th>
                            <th class="py-3 px-6 font-semibold">Timeline Proyek</th>
                            <th class="py-3 px-6 font-semibold">Status Dokumen PO</th>
                            <th class="py-3 px-6 font-semibold text-center">Status Siklus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @forelse($projects as $p)
                        <tr>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $p['projectName'] ?? '-' }}<br>
                                <span class="font-mono font-normal text-[10px] text-slate-400">{{ $p['canonicalCode'] ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-medium">
                                {{ $p['clientName'] ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                AM: <strong class="text-slate-900">{{ $p['amName'] ?? '-' }}</strong><br>
                                PM: <strong class="text-slate-900">{{ $p['pmName'] ?? '-' }}</strong>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-medium">
                                Mulai: {{ $p['startDate'] ?? '-' }}<br>
                                <span class="text-[10px] text-slate-400">Target: {{ $p['targetEndDate'] ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $poReceived = $p['poReceived'] ?? false;
                                @endphp
                                @if($poReceived)
                                    <span class="bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full text-[10px] font-bold flex items-center gap-1 w-max">
                                        ● PO_ADA (Valid)
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-2.5 py-1 rounded-full text-[10px] font-bold flex items-center gap-1 w-max">
                                        ✕ PO_TIDAK_ADA (Blokir Termin)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="bg-slate-100 text-slate-700 font-semibold px-2.5 py-1 rounded text-[10px]">
                                    {{ $p['status'] ?? 'REGISTERED' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500 font-medium bg-slate-50/50">
                                ⚠️ Tidak ada data proyek ditemukan untuk status filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>
@endsection