<x-app-layout title="ধূমপান">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">ধূমপান ট্র্যাকার</h2>
        <p class="text-xs text-gray-500 mt-1">
            {{ \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>

    {{-- লক্ষ্য --}}
    @php
        $artDay = $user->daysSinceArtStart() + 1;
        if ($artDay <= 60)      { $target = 'দিনে ৪টি'; $month = 'মাস ১-২'; }
        elseif ($artDay <= 90)  { $target = 'দিনে ৩টি'; $month = 'মাস ৩'; }
        elseif ($artDay <= 120) { $target = 'দিনে ২টি'; $month = 'মাস ৪'; }
        elseif ($artDay <= 150) { $target = 'দিনে ১টি'; $month = 'মাস ৫'; }
        else                    { $target = 'সম্পূর্ণ বন্ধ 🎯'; $month = 'মাস ৬'; }
    @endphp

    <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3 mb-4 flex items-center gap-3">
        <div class="text-center">
            <div class="text-xl font-bold text-amber-400">{{ $artDay }}</div>
            <div class="text-xs text-amber-700">দিন</div>
        </div>
        <div>
            <p class="text-sm text-amber-400 font-medium">{{ $month }} এর লক্ষ্য: {{ $target }}</p>
            <p class="text-xs text-gray-500">নিচে টিক দিন যেটা খেয়েছেন</p>
        </div>
    </div>

    {{-- নির্দেশনা --}}
    <div class="flex gap-3 mb-3 text-xs text-gray-500">
        <span class="flex items-center gap-1">
            <span class="w-4 h-4 rounded-full bg-emerald-500 inline-block"></span>
            সম্পন্ন = খেয়েছি
        </span>
        <span class="flex items-center gap-1">
            <span class="w-4 h-4 rounded-full border-2 border-gray-600 inline-block"></span>
            খালি = খাইনি ✅
        </span>
    </div>

    <x-task-list
        :tasks="$tasks"
        :logs="$logs"
        route="smoking"
        :isSmokingPage="true" 
    />

    <form method="POST" action="{{ route('smoking.extra.save') }}" class="bg-gray-900 border border-gray-800 rounded-xl p-4 mt-4 space-y-4">
        @csrf
    
        <div>
            <label for="extra_cigarettes" class="block text-sm font-medium text-white mb-2">
                আজ এক্সট্রা কতটি সিগারেট খেয়েছেন?
            </label>
            <input
                id="extra_cigarettes"
                type="number"
                name="extra_cigarettes"
                min="0"
                step="1"
                value="{{ old('extra_cigarettes', $extraCigarettes ?? 0) }}"
                class="w-full rounded-xl bg-gray-950 border border-gray-700 text-white px-4 py-3"
                placeholder="যেমন 1 / 2 / 3"
            >
            @error('extra_cigarettes')
                <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>
    
        <button type="submit" class="w-full rounded-xl bg-amber-600 hover:bg-amber-500 text-white py-3 text-sm font-medium transition">
            এক্সট্রা সেভ করুন
        </button>
    </form>

    {{-- আজকের সারসংক্ষেপ --}}
    @php
        $smokedCount = $logs->filter()->count();
        $extraCount  = $extraCigarettes ?? 0;
        $total       = $tasks->count();
        $avoided     = $total - $smokedCount;
        $totalSmoked = $smokedCount + $extraCount;
    @endphp

    <div class="grid grid-cols-2 gap-3 mt-4">
        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-red-400">{{ $totalSmoked }}</div>
            <div class="text-xs text-gray-500 mt-1">আজ মোট খেয়েছি</div>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-400">{{ $avoided }}</div>
            <div class="text-xs text-gray-500 mt-1">আজ এড়িয়েছি</div>
        </div>
    </div>

    @if($avoided === $total && $total > 0)
        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4 mt-4 text-center">
            <p class="text-emerald-400 text-sm font-semibold">🌟 অসাধারণ! আজকে একটাও খাননি!</p>
        </div>
    @endif

</x-app-layout>