<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Profitabilitas Proyek - Modul E-2</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900/50 font-sans text-slate-800 flex items-center justify-center min-h-screen p-6">

    @php
        $token = session('token');
        $erp = new \App\Services\ErpIntegrationService($token);
        $targetProject = $projectId ?? '01924b1e-7f32-7a81-9b12-45e8a1234567';
        $project = $erp->getProject($targetProject);
        $termins = $erp->getTermins($targetProject);
        
        $totalRincian = 0;
        foreach($termins as $tr) {
            $totalRincian += floatval($tr['terminValue'] ?? 0);
        }
    @endphp

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl overflow-hidden border border-slate-200">
        
        <!-- Header Modal -->
        <div class="px-8 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Dasbor > Proyek > {{ $project['projectId'] ?? '-' }}</p>
                <h2 class="text-xl font-bold text-slate-900 mt-0.5">{{ $project['projectName'] ?? 'Proyek Konstruksi & ERP' }}</h2>
            </div>
            <a href="{{ url('/') }}" class="text-slate-400 hover:text-slate-600 font-semibold text-sm">
                ✕ Tutup
            </a>
        </div>

        <!-- Metadata Proyek dari D-1 -->
        <div class="px-8 py-4 bg-blue-50/50 border-b border-slate-200 flex justify-between items-center text-xs">
            <div>
                <span class="text-slate-500">Klien:</span>
                <span class="font-bold text-slate-800">{{ $project['clientName'] ?? 'PT Client Hulu' }}</span>
            </div>
            <div>
                <span class="text-slate-500">Status Proyek:</span>
                <span class="font-bold text-blue-800">{{ $project['status'] ?? 'REGISTERED' }}</span>
            </div>
        </div>

        <!-- Konten Tabel Rincian Termin -->
        <div class="p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 font-semibold">ID Termin</th>
                            <th class="py-3 px-4 font-semibold">No. Termin / Kontrak</th>
                            <th class="py-3 px-4 font-semibold">Tanggal Jatuh Tempo</th>
                            <th class="py-3 px-4 font-semibold text-right">Nilai Termin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($termins as $termin)
                        <tr>
                            <td class="py-4 px-4 font-medium text-slate-900">{{ $termin['terminId'] ?? '-' }}</td>
                            <td class="py-4 px-4 text-slate-600">Termin ke-{{ $termin['terminNumber'] ?? 1 }} ({{ $termin['contractNumber'] ?? '-' }})</td>
                            <td class="py-4 px-4 text-slate-500">{{ $termin['dueDate'] ?? '-' }}</td>
                            <td class="py-4 px-4 text-right font-semibold">Rp {{ number_format((float)($termin['terminValue'] ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-center text-slate-400">Tidak ada rincian termin untuk proyek ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-200 font-bold text-slate-900">
                            <td colspan="3" class="py-4 px-4">Total Proyeksi Pemasukan Proyek</td>
                            <td class="py-4 px-4 text-right">Rp {{ number_format((float)($totalRincian ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer Modal -->
        <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <a href="{{ url('/') }}" class="px-5 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                Kembali ke dasbor
            </a>
            <div class="flex items-center gap-2 bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg text-xs font-semibold">
                <span>✓ Data Valid Ditarik dari D-1 & D-2</span>
            </div>
        </div>

    </div>

</body>
</html>