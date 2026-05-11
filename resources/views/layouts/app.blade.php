<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'আমার স্বাস্থ্য পরিকল্পনা' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white min-h-screen font-bengali pb-20"
      x-data="{ page: '{{ request()->routeIs('dashboard') ? 'dashboard' : (request()->routeIs('smoking*') ? 'smoking' : (request()->routeIs('medicine*') ? 'medicine' : (request()->routeIs('monthly*') ? 'monthly' : 'dashboard'))) }}' }">

    {{-- Top Bar --}}
    <div class="sticky top-0 z-50 bg-gray-900 border-b border-gray-800">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-base font-semibold text-white">আমার স্বাস্থ্য পরিকল্পনা</h1>
                    <p class="text-xs text-gray-400" id="currentDate"></p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- ART দিন কাউন্টার --}}
                    @auth
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg px-3 py-1 text-center">
                        <div class="text-emerald-400 text-sm font-bold">দিন {{ Auth::user()->daysSinceArtStart() + 1 }}</div>
                        <div class="text-emerald-600 text-xs">ART</div>
                    </div>
                    @endauth
                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            {{-- Progress Bar --}}
            @isset($percentage)
            <div class="mt-2 h-1 bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                     style="width: {{ $percentage }}%"></div>
            </div>
            @endisset
        </div>
    </div>

    {{-- Main Content --}}
    <main class="max-w-lg mx-auto px-4 py-4">
        {{ $slot }}
    </main>

    {{-- Bottom Navigation --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-gray-900 border-t border-gray-800 z-50">
        <div class="max-w-lg mx-auto flex">

            {{-- ঔষধ --}}
            <a href="{{ route('medicine.index') }}"
            class="flex-1 flex flex-col items-center py-3 gap-1 transition
                    {{ request()->routeIs('medicine*') ? 'text-emerald-400' : 'text-gray-500 hover:text-gray-300' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-xs">ঔষধ</span>
            </a>

            {{-- নামাজ --}}
            <a href="{{ route('prayer.index') }}"
            class="flex-1 flex flex-col items-center py-3 gap-1 transition
                    {{ request()->routeIs('prayer*') ? 'text-emerald-400' : 'text-gray-500 hover:text-gray-300' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M12 3v1m0 16v1m8.66-13l-.87.5M4.21 15.5l-.87.5M20.66 15.5l-.87-.5M4.21 8.5l-.87-.5M21 12h-1M4 12H3"/>
                </svg>
                <span class="text-xs">নামাজ</span>
            </a>

            {{-- ব্যায়াম --}}
            <a href="{{ route('exercise.index') }}"
            class="flex-1 flex flex-col items-center py-3 gap-1 transition
                    {{ request()->routeIs('exercise*') ? 'text-emerald-400' : 'text-gray-500 hover:text-gray-300' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span class="text-xs">ব্যায়াম</span>
            </a>

            {{-- ধূমপান --}}
            <a href="{{ route('smoking.index') }}"
            class="flex-1 flex flex-col items-center py-3 gap-1 transition
                    {{ request()->routeIs('smoking*') ? 'text-emerald-400' : 'text-gray-500 hover:text-gray-300' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M3 14h14M3 18h14M17 14v4M20 14v4M17 10c0-2 1-3 3-3"/>
                </svg>
                <span class="text-xs">ধূমপান</span>
            </a>

            {{-- মাসিক --}}
            <a href="{{ route('monthly.index') }}"
            class="flex-1 flex flex-col items-center py-3 gap-1 transition
                    {{ request()->routeIs('monthly*') ? 'text-emerald-400' : 'text-gray-500 hover:text-gray-300' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xs">মাসিক</span>
            </a>

        </div>
    </nav>

    <script>
        // তারিখ দেখানো
        const days = ['রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'];
        const months = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        const now = new Date();
        const el = document.getElementById('currentDate');
        if (el) el.textContent = days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
    </script>
</body>
</html>