<x-app-layout title="ওষুধ ট্র্যাকার">

    {{-- ART Streak --}}
    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-4 text-center">
        <div class="text-3xl font-bold text-emerald-400">{{ $streak }}</div>
        <div class="text-sm text-emerald-600 mt-1">দিন ধারাবাহিকভাবে ART নিয়েছেন</div>
        @if($streak > 0)
            <div class="text-xs text-gray-500 mt-1">💪 চালিয়ে যান!</div>
        @else
            <div class="text-xs text-gray-500 mt-1">আজকে থেকে শুরু হোক!</div>
        @endif
    </div>

    {{-- আজকের ওষুধ --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">আজকের ওষুধ</h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
        @foreach($medicines as $key => $medicine)
            @php
                $log    = $logs->get($key);
                $taken  = $log && $log->status === 'taken';
                $missed = $log && $log->status === 'missed';
            @endphp
            <div class="flex items-center gap-3 px-4 py-4
                        {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                {{-- আইকন --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                            {{ $taken ? 'bg-emerald-500/20' : 'bg-gray-800' }}">
                    <svg class="w-5 h-5 {{ $taken ? 'text-emerald-400' : 'text-gray-500' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>

                {{-- তথ্য --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium {{ $taken ? 'text-gray-400 line-through' : 'text-white' }}">
                        {{ $medicine['label'] }}
                    </p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        নির্ধারিত সময়: {{ \Carbon\Carbon::parse($medicine['time'])->format('h:i A') }}
                        @if($taken && $log->taken_at)
                            · নেওয়া হয়েছে {{ $log->taken_at->format('h:i A') }}
                        @endif
                    </p>
                </div>

                {{-- বোতাম --}}
                <button onclick="toggleMedicine('{{ $key }}', this)"
                        data-medicine="{{ $key }}"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition
                               {{ $taken
                                   ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
                                   : 'bg-gray-800 text-gray-400 border border-gray-700 hover:border-emerald-500/50' }}">
                    {{ $taken ? '✅ নেওয়া হয়েছে' : '💊 নিন' }}
                </button>
            </div>
        @endforeach
    </div>

    {{-- গত ৩০ দিনের ইতিহাস --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">গত ৩০ দিনের ইতিহাস</h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        @forelse($history as $date => $dayLogs)
            <div class="flex items-center justify-between px-4 py-3
                        {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                <div>
                    <p class="text-sm text-white">
                        {{ \Carbon\Carbon::parse($date)->locale('bn')->isoFormat('D MMMM') }}
                    </p>
                    <p class="text-xs text-gray-600 mt-0.5">
                        {{ \Carbon\Carbon::parse($date)->locale('bn')->isoFormat('dddd') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    @foreach($dayLogs as $log)
                        <span class="text-xs px-2 py-1 rounded-lg
                                     {{ $log->status === 'taken'
                                         ? 'bg-emerald-500/10 text-emerald-400'
                                         : ($log->status === 'missed'
                                             ? 'bg-red-500/10 text-red-400'
                                             : 'bg-gray-800 text-gray-500') }}">
                            {{ $log->medicine_name }}
                            {{ $log->status === 'taken' ? '✓' : ($log->status === 'missed' ? '✗' : '?') }}
                        </span>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center">
                <p class="text-gray-600 text-sm">এখনো কোনো ইতিহাস নেই</p>
                <p class="text-gray-700 text-xs mt-1">আজকে থেকে শুরু হোক!</p>
            </div>
        @endforelse
    </div>

    {{-- গুরুত্বপূর্ণ নোট --}}
    <div class="bg-amber-500/5 border border-amber-500/10 rounded-xl p-4 mt-4">
        <p class="text-amber-400 text-xs font-semibold mb-1">⚠️ মনে রাখবেন</p>
        <p class="text-gray-500 text-xs leading-relaxed">
            ART ওষুধ প্রতিদিন রাত ৮টায় নিন। একটি দিনও মিস করবেন না।
            TB ওষুধ সকাল ১০:৩০ এ খালি পেটে নিন।
        </p>
    </div>

</x-app-layout>

<script>
function toggleMedicine(medicine, btn) {
    const original = btn.innerHTML;
    btn.innerHTML = '⏳';
    btn.disabled  = true;

    fetch('/medicine/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ medicine }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(() => {
        btn.innerHTML = original;
        btn.disabled  = false;
    });
}
</script>