@extends('layouts.app')

@section('title', 'Pemasukan - Modul E-2')
@section('header-title', 'Proyeksi & Realisasi Pemasukan Proyek')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if(isset($isSyncFailed) && $isSyncFailed)
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center space-y-4">
            <h3 class="text-lg font-bold text-slate-900">Data pemasukan belum tersedia</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">Gagal mengambil data termin dari D-2.</p>
            <button onclick="window.location.reload();" class="px-5 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                Muat ulang halaman dasbor
            </button>
        </div>
    @else
        <!-- Grafik Batang Kontinu 6 Bulan (Zero-Fill) -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 space-y-6">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pemasukan: proyeksi dari termin</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Tren proyeksi kas masuk bulanan (Juli 2026 s.d. Desember 2026)</p>
                </div>
            </div>

            <!-- Area Grafik Batang dengan Zero-Fill (Bulan tanpa data bernilai 0) -->
            <div class="h-64 flex items-end justify-between gap-4 border-b border-slate-200 pb-3 px-4 pt-6 overflow-x-auto">
                @foreach($monthlyIncome as $key => $data)
                    @php
                        $totalVal = $data['total'];
                        // Logika tinggi batang proporsional sederhana (maksimal atau 0 jika kosong)
                        $heightStyle = $totalVal > 0 ? 'height: 160px;' : 'height: 4px;';
                        $barColor = $totalVal > 0 ? 'bg-blue-900' : 'bg-slate-200';
                    @endphp

                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end min-w-[90px]">
                        <div class="w-full flex items-end justify-center gap-1.5 h-44 bg-slate-50 rounded-xl p-1 border border-slate-100">
                            <div class="w-6 {{ $barColor }} rounded-t-sm transition-all duration-300" style="{{ $heightStyle }}" title="{{ $data['label'] }}: Rp {{ number_format($totalVal, 0, ',', '.') }}"></div>
                        </div>
                        <div class="text-center">
                            <span class="block text-xs font-bold text-slate-800">{{ $data['label'] }}</span>
                            <span class="text-[10px] font-semibold {{ $totalVal > 0 ? 'text-blue-900' : 'text-slate-400' }}">
                                @if($totalVal > 0)
                                    Rp {{ number_format($totalVal / 1000000, 1, ',', '.') }} Jt
                                @else
                                    Rp 0 (Kosong)
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tabel Rincian Termin dengan Kolom Nama Proyek & Klien -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-base font-bold text-slate-900">Daftar Termin & Rincian Arus Kas Masuk</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase tracking-wider">
                            <th class="py-3 px-6 font-semibold">Proyek & Klien</th>
                            <th class="py-3 px-6 font-semibold">ID Termin / Kontrak</th>
                            <th class="py-3 px-6 font-semibold">Jatuh Tempo</th>
                            <th class="py-3 px-6 font-semibold">Nilai Termin</th>
                            <th class="py-3 px-6 font-semibold">Status Penagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 text-xs">
                        @forelse($liveTermins as $t)
                        <tr>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $t['projectName'] ?? '-' }}<br>
                                <span class="font-normal text-[11px] text-slate-500">Klien: {{ $t['clientName'] ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 font-mono">
                                {{ $t['terminId'] ?? '-' }}<br>
                                <span class="text-[10px] text-slate-400 font-sans">Kontrak: {{ $t['contractNumber'] ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-600">{{ $t['dueDate'] ?? '-' }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900">Rp {{ number_format(floatval($t['terminValue'] ?? 0), 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                <span class="bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full font-semibold">
                                    {{ $t['status'] ?? 'SIAP_DITAGIH' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada data pemasukan termin dari server hulu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection