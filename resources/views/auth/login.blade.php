<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Dasbor Keuangan Manajemen (Modul E-2)</title>
    <!-- Tailwind Play CDN resmi -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans text-slate-800 flex items-center justify-center h-screen m-0">

    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 w-full max-w-md space-y-6">
        
        <!-- Header Logo & Judul -->
        <div class="space-y-2">
            <div class="w-10 h-10 bg-blue-900 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-sm">
                🏢
            </div>
            <h2 class="text-xl font-bold text-slate-900">Login</h2>
        </div>

        <!-- Alert Error Global / Kredensial Salah -->
        @if($errors->has('login_error'))
            <div class="bg-red-50 border border-red-200 text-red-600 text-xs p-3 rounded-lg font-medium text-center">
                {{ $errors->first('login_error') }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ url('/login') }}" method="POST" class="space-y-4" onsubmit="handleLoginSubmit(this)">
            @csrf
            
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email / Username</label>
                <input type="text" name="emailOrUsername" placeholder="email atau username" value="{{ old('emailOrUsername') }}" required
                    class="w-full px-4 py-2.5 bg-white border {{ $errors->has('emailOrUsername') ? 'border-red-300 bg-red-50/30' : 'border-slate-300' }} rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 transition">
                @error('emailOrUsername')
                    <span class="text-[11px] text-red-600 font-medium">{{ $message }}</span>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kata Sandi</label>
                <!-- Pembungkus Relative untuk Input & Tombol Mata -->
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="kata sandi" required
                        class="w-full pl-4 pr-12 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-900 transition">
                    
                    <!-- Tombol Toggle Hide/Unhide -->
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                        <!-- Ikon Mata Tertutup (Default) -->
                        <svg id="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                        <!-- Ikon Mata Terbuka (Hidden by default) -->
                        <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="w-full py-3 bg-blue-900 hover:bg-blue-800 text-white rounded-xl font-bold text-sm shadow-sm transition flex items-center justify-center space-x-2">
                <span>Masuk</span>
            </button>
        </form>
    </div>

    <!-- Script JavaScript untuk Toggle & Loading -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeSlashIcon.classList.add('hidden');
                eyeIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeSlashIcon.classList.remove('hidden');
                eyeIcon.classList.add('hidden');
            }
        }

        function handleLoginSubmit(form) {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.className = "w-full py-3 bg-blue-900/70 text-white rounded-xl font-bold text-sm shadow-sm flex items-center justify-center space-x-2 cursor-not-allowed";
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Memproses...</span>
            `;
        }
    </script>
</body>
</html>