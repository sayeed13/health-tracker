<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center font-bengali px-4">
    <div class="w-full max-w-sm">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">আমার স্বাস্থ্য পরিকল্পনা</h1>
            <p class="text-gray-500 text-sm mt-1">আপনার সুস্বাস্থ্যের যাত্রা</p>
        </div>

        {{-- Form --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6">
            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm text-gray-400 mb-2">ইমেইল</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                  text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500
                                  transition text-sm"
                           placeholder="আপনার ইমেইল">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm text-gray-400 mb-2">পাসওয়ার্ড</label>
                    <input type="password" name="password" required
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3
                                  text-white placeholder-gray-600 focus:outline-none focus:border-emerald-500
                                  transition text-sm"
                           placeholder="আপনার পাসওয়ার্ড">
                </div>

                <button type="submit"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold
                               py-3 rounded-xl transition text-sm">
                    লগইন করুন
                </button>
            </form>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">
            আপনার স্বাস্থ্য, আপনার অগ্রগতি 💚
        </p>
    </div>
</body>
</html>