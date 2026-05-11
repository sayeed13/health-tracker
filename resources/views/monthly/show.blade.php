<x-app-layout title="মাসিক প্রগ্রেস">

    {{-- মাস নেভিগেশন --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('monthly.show', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
           class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-sm text-gray-400
                  hover:text-white transition">
            ← আগে
        </a>
        <div class="text-center">
            <h2 class="text-base font-semibold text-white">
                {{ $carbon->locale('bn')->isoFormat('MMMM YYYY') }}
            </h2>
        </div>
        @if($carbon->lt($now->startOfMonth()))
            <a href="{{ route('monthly.show', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
               class="bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-sm text-gray-400
                      hover:text-white transition">
                পরে →
            </a>
        @else
            <div class="w-20"></div>
        @endif
    </div>

    {{-- ক্যালেন্ডার গ্রিড --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        {{-- দিনের হেডার --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['রবি','সোম','মঙ্গল','বুধ','বৃহ','শুক্র','শনি'] as $day)
                <div class="text-center text-xs text-gray-600 py-1">{{ $day }}</div>
            @endforeach
        </div>

        {{-- খালি ঘর --}}
        <div class="grid grid-cols-7 gap-1">
            @php $firstDay = $carbon->copy()->startOfMonth()->dayOfWeek; @endphp
            @for($i = 0; $i < $firstDay; $i++)
                <div></div>
            @endfor

            {{-- দিনগুলো --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $data    = $dailyData[$d];
                    $pct     = $data['pct'];
                    $isToday = $carbon->copy()->setDay($d)->isToday();
                    $isFuture = $carbon->copy()->setDay($d)->isFuture();

                    $bgClass = $isFuture ? 'bg-gray-800 text-gray-700'
                             : ($data['total'] === 0 ? 'bg-gray-800 text-gray-600'
                             : ($pct >= 70 ? 'bg-emerald-500/30 text-emerald-300'
                             : ($pct >= 40 ? 'bg-emerald-500/15 text-emerald-500'
                             : 'bg-amber-500/15 text-amber-500')));
                @endphp
                <div class="aspect-square rounded-lg flex items-center justify-center text-xs
                            {{ $bgClass }}
                            {{ $isToday ? 'ring-2 ring-emerald-500' : '' }}"
                     title="{{ $pct }}%">
                    {{ $d }}
                </div>
            @endfor
        </div>

        {{-- লেজেন্ড --}}
        <div class="flex gap-3 mt-3 flex-wrap">
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-emerald-500/30"></div>
                <span class="text-xs text-gray-500">দারুণ (৭০%+)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-emerald-500/15"></div>
                <span class="text-xs text-gray-500">ভালো (৪০%+)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-amber-500/15"></div>
                <span class="text-xs text-gray-500">কম (৪০%-)</span>
            </div>
        </div>
    </div>

    {{-- ক্যাটাগরি প্রগ্রেস বার --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">ক্যাটাগরি অনুযায়ী</h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        @php
            $categories = [
                'medicine' => ['label' => 'ওষুধ',    'color' => 'bg-red-500'],
                'prayer'   => ['label' => 'নামাজ',   'color' => 'bg-purple-500'],
                'exercise' => ['label' => 'ব্যায়াম', 'color' => 'bg-blue-500'],
                'food'     => ['label' => 'খাবার',   'color' => 'bg-amber-500'],
                'water'    => ['label' => 'পানি',    'color' => 'bg-cyan-500'],
                'review'   => ['label' => 'রিভিউ',   'color' => 'bg-gray-500'],
            ];
        @endphp
        <div class="space-y-3">
            @foreach($categories as $key => $cat)
                @php
                    $stat = $stats->get($key);
                    $rate = $stat ? $stat->completion_rate : 0;
                @endphp
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-xs text-gray-400">{{ $cat['label'] }}</span>
                        <span class="text-xs text-gray-500">{{ number_format($rate, 0) }}%</span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full {{ $cat['color'] }} rounded-full transition-all"
                             style="width: {{ $rate }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ধূমপান সারসংক্ষেপ --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">ধূমপান সারসংক্ষেপ</h3>
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-red-400">
                {{ $smokingData->get('smoked', 0) }}
            </div>
            <div class="text-xs text-gray-500 mt-1">মোট খেয়েছি</div>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-400">
                {{ $smokingData->get('skipped', 0) }}
            </div>
            <div class="text-xs text-gray-500 mt-1">মোট এড়িয়েছি</div>
        </div>
    </div>

    {{-- ART সারসংক্ষেপ --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">ART ওষুধ</h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        @php
            $artTaken  = $medicineData->get('taken', 0);
            $artMissed = $medicineData->get('missed', 0);
            $artTotal  = $artTaken + $artMissed;
            $artRate   = $artTotal > 0 ? round(($artTaken / $artTotal) * 100) : 0;
        @endphp
        <div class="flex justify-between mb-2">
            <span class="text-sm text-white">নিয়মিততা</span>
            <span class="text-sm font-bold {{ $artRate >= 95 ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $artRate }}%
            </span>
        </div>
        <div class="h-3 bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full bg-emerald-500 rounded-full"
                 style="width: {{ $artRate }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>নিয়েছি: {{ $artTaken }} দিন</span>
            <span>মিস: {{ $artMissed }} দিন</span>
        </div>
        @if($artRate >= 95)
            <p class="text-emerald-400 text-xs text-center mt-3">
                🎉 অসাধারণ! ৯৫%+ নিয়মিততা — ড্রাগ রেজিস্ট্যান্সের কোনো ঝুঁকি নেই!
            </p>
        @elseif($artRate >= 80)
            <p class="text-amber-400 text-xs text-center mt-3">
                ⚠️ ভালো, কিন্তু আরো নিয়মিত হোন। লক্ষ্য ৯৫%+
            </p>
        @else
            <p class="text-red-400 text-xs text-center mt-3">
                ❗ অনুগ্রহ করে প্রতিদিন ART নিন। এটা অত্যন্ত জরুরি।
            </p>
        @endif
    </div>

</x-app-layout>