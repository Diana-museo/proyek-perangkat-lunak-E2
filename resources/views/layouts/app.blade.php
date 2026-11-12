<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor Keuangan Manajemen') - Modul E-2</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 flex h-screen overflow-hidden">

    <!-- SIDEBAR UTAMA -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between hidden md:flex shrink-0">
        <div>
            <!-- Logo / Brand -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center space-x-3">
                <div class="bg-blue-900 text-white p-2 rounded-lg font-bold text-sm">🏢</div>
                <span class="font-bold text-slate-900 text-base">Dasbor Keuangan</span>
            </div>

            <!-- Menu Navigasi -->
            <div class="p-4 space-y-1">
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Menu Utama</p>
                
                <a href="{{ url('/dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ Request::is('dashboard') || Request::is('/') ? 'bg-blue-50 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>📊</span>
                    <span>Dasbor Utama</span>
                </a>
                
                <a href="{{ url('/dashboard/pemasukan') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ Request::is('dashboard/pemasukan*') ? 'bg-blue-50 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>📈</span>
                    <span>Pemasukan</span>
                </a>
                
                <a href="{{ url('/dashboard/pengeluaran') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ Request::is('dashboard/pengeluaran*') ? 'bg-blue-50 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>📉</span>
                    <span>Pengeluaran</span>
                </a>

                <a href="{{ url('/dashboard/proyek') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ Request::is('dashboard/proyek*') ? 'bg-blue-50 text-blue-900 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span>🏗️</span>
                    <span>Proyek</span>
                </a>
            </div>
        </div>

        <!-- Footer Sidebar: Tombol Keluar & Status Modul E-2 -->
        <div class="p-4 border-t border-slate-100 space-y-3">
            <!-- Tombol Keluar di Sidebar -->
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-lg text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                    <span>Keluar</span>
                </button>
            </form>

            <!-- Kotak Modul E-2 -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-700 mb-1">
                    <span>Modul E-2</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                </div>
                <p class="text-[10px] text-slate-400">Stateless Aggregator</p>
            </div>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        
        <!-- Navbar Atas (Tanpa Tombol Keluar) -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm shrink-0">
            <h1 class="text-lg font-bold text-slate-900">@yield('header-title', 'Ringkasan Eksekutif')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium bg-slate-100 px-3 py-1.5 rounded-full text-slate-700">
                    👤 {{ session('userName', 'Budi Santoso') }} <span class="text-slate-400">| {{ session('userRole', 'Kepala Finance') }}</span>
                </span>
            </div>
        </header>

        <!-- Area Scroll Konten -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-50">
            @yield('content')
        </main>
        
        <!-- Footer Bawah -->
        <footer class="bg-white border-t border-slate-200 px-8 py-3 text-xs text-slate-400 flex justify-between items-center shrink-0">
            <span>© 2026 PT Anaheim Nimbus Universal · Modul E-2</span>
            <div class="flex items-center space-x-3">
                <span id="sync-time-text">Sinkronisasi terakhir: <span id="dynamic-timestamp">-</span></span>
                
                <div id="sync-status-container" class="inline-flex items-center gap-2">
                    <!-- Badge Status & Tombol Refresh -->
                    <span id="sync-badge" class="hidden px-2 py-0.5 rounded border text-[11px] font-bold"></span>
                    
                    <button id="refresh-sync-btn" onclick="executeGlobalRefresh()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded border border-slate-300 font-semibold transition flex items-center gap-1">
                        🔄 Refresh Data
                    </button>
                </div>
            </div>
        </footer>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Set timestamp awal dari server/session
                document.getElementById('dynamic-timestamp').innerText = "{{ date('d M Y, H:i:s') }} WIB";
            });

            async function executeGlobalRefresh() {
                const btn = document.getElementById('refresh-sync-btn');
                const badge = document.getElementById('sync-badge');
                
                // 1. Tampilkan indikator loading (sejalan dengan durasi asli pengambilan data)
                btn.disabled = true;
                btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-slate-700 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengambil Data Hulu...`;
                badge.classList.add('hidden');

                try {
                    // 2. Lempar request AJAX ke Controller Backend Modul E-2
                    const response = await fetch('/dashboard/api/refresh');
                    const result = await response.json();

                    // 3. Perbarui timestamp dinamis sesuai waktu eksekusi aktual
                    document.getElementById('dynamic-timestamp').innerText = result.timestamp;

                    // 4. Ubah status badge berdasarkan hasil validasi backend
                    badge.classList.remove('hidden');
                    if (result.status === 'synced') {
                        badge.className = "px-2 py-0.5 rounded border text-[11px] font-bold bg-emerald-50 text-emerald-700 border-emerald-200";
                        badge.innerText = "SYNCED";
                    } else if (result.status === 'fallback') {
                        badge.className = "px-2 py-0.5 rounded border text-[11px] font-bold bg-amber-50 text-amber-700 border-amber-200";
                        badge.innerText = "DEFAULT DATA (FALLBACK)";
                    } else {
                        badge.className = "px-2 py-0.5 rounded border text-[11px] font-bold bg-red-50 text-red-700 border-red-200";
                        badge.innerText = "SYNC FAILED";
                    }

                } catch (error) {
                    badge.classList.remove('hidden');
                    badge.className = "px-2 py-0.5 rounded border text-[11px] font-bold bg-red-50 text-red-700 border-red-200";
                    badge.innerText = "SYNC FAILED";
                } finally {
                    // 5. Kembalikan tombol ke semula setelah 5 detik
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = `🔄 Refresh Data`;
                        badge.classList.add('hidden');
                    }, 5000);
                }
            }
        </script>
    </div>

</body>
</html>