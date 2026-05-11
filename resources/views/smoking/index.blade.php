<x-app-layout title="ধূমপান ট্র্যাকার">

    {{-- লক্ষ্য --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-white">৬ মাসের লক্ষ্য</h3>
            <span class="text-xs text-gray-500">সম্পূর্ণ বন্ধ 🎯</span>
        </div>
        @php
            $targets = [
                'মাস ১-২' => 'দিনে ৪টি',
                'মাস ৩'   => 'দিনে ৩টি',
                'মাস ৪'   => 'দিনে ২টি',
                'মাস ৫'   => 'দিনে ১টি',
                'মাস ৬'   => 'সম্পূর্ণ বন্ধ',
            ];
        @endphp
        <div class="space-y-2">
            @foreach($targets as $month => $target)
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500">{{ $month }}</span>
                    <span class="text-xs text-gray-400">{{ $target }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- আজকের ট্র্যাকার --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">আজকের ধূমপান</h3>
    <p class="text-xs text-gray-600 mb-3">
        একবার ট্যাপ = খেয়েছি | দুইবার = এড়িয়েছি | তিনবার = রিসেট
    </p>

    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-4">
        @foreach($slots as $slot => $label)
            @php
                $status = $logs->get($slot, 'pending');
            @endphp
            <div class="flex items-center justify-between px-4 py-3
                        {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                <p class="text-sm {{ $status === 'skipped' ? 'text-gray-500 line-through' : 'text-white' }}">
                    {{ $label }}
                </p>
                <button onclick="toggleSmoke('{{ $slot }}', this)"
                        data-slot="{{ $slot }}"
                        data-status="{{ $status }}"
                        class="w-10 h-10 rounded-full border flex items-center justify-center
                               text-sm transition font-semibold
                               {{ $status === 'smoked'
                                   ? 'bg-red-500/20 border-red-500/30 text-red-400'
                                   : ($status === 'skipped'
                                       ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400'
                                       : 'bg-gray-800 border-gray-700 text-gray-500') }}">
                    {{ $status === 'smoked' ? '✗' : ($status === 'skipped' ? '✓' : '?') }}
                </button>
            </div>
        @endforeach
    </div>

    {{-- আজকের সারসংক্ষেপ --}}
    @php
        $smokedCount  = $logs->filter(fn($s) => $s === 'smoked')->count();
        $skippedCount = $logs->filter(fn($s) => $s === 'skipped')->count();
    @endphp
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-red-400">{{ $smokedCount }}</div>
            <div class="text-xs text-gray-500 mt-1">আজ খেয়েছি</div>
        </div>
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-400">{{ $skippedCount }}</div>
            <div class="text-xs text-gray-500 mt-1">আজ এড়িয়েছি</div>
        </div>
    </div>

    {{-- গত ৩০ দিনের ইতিহাস --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">গত ৩০ দিন</h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        @forelse($history as $date => $dayLogs)
            @php
                $daySmoked  = $dayLogs->filter(fn($l) => $l->status === 'smoked')->count();
                $daySkipped = $dayLogs->filter(fn($l) => $l->status === 'skipped')->count();
            @endphp
            <div class="flex items-center justify-between px-4 py-3
                        {{ !$loop->last ? 'border-b border-gray-800' : '' }}">
                <div>
                    <p class="text-sm text-white">
                        {{ \Carbon\Carbon::parse($date)->locale('bn')->isoFormat('D MMMM') }}
                    </p>
                    <p class="text-xs text-gray-600">
                        {{ \Carbon\Carbon::parse($date)->locale('bn')->isoFormat('dddd') }}
                    </p>
                </div>
                <div class="flex gap-3 text-xs">
                    <span class="text-red-400">✗ {{ $daySmoked }}</span>
                    <span class="text-emerald-400">✓ {{ $daySkipped }}</span>
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center">
                <p class="text-gray-600 text-sm">এখনো কোনো ইতিহাস নেই</p>
            </div>
        @endforelse
    </div>

    {{-- উৎসাহ --}}
    @if($skippedCount > $smokedCount)
        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4 mt-4 text-center">
            <p class="text-emerald-400 text-sm">💪 দারুণ! আজকে বেশি এড়িয়েছেন!</p>
        </div>
    @elseif($smokedCount === 0 && $skippedCount > 0)
        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4 mt-4 text-center">
            <p class="text-emerald-400 text-sm">🌟 অসাধারণ! আজকে একটাও খাননি!</p>
        </div>
    @endif

</x-app-layout>

<script>
function toggleSmoke(slot, btn) {
    const current = btn.dataset.status;
    const next    = current === 'pending' ? 'smoked'
                  : current === 'smoked'  ? 'skipped'
                  : 'pending';

    btn.dataset.status = next;
    btn.innerHTML = next === 'smoked' ? '✗' : next === 'skipped' ? '✓' : '?';
    btn.className = `w-10 h-10 rounded-full border flex items-center justify-center
                     text-sm transition font-semibold ${
                       next === 'smoked'
                         ? 'bg-red-500/20 border-red-500/30 text-red-400'
                         : next === 'skipped'
                           ? 'bg-emerald-500/20 border-emerald-500/30 text-emerald-400'
                           : 'bg-gray-800 border-gray-700 text-gray-500'
                     }`;

    fetch('/smoking/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ slot }),
    })
    .then(res => res.json())
    .then(data => {
        // সারসংক্ষেপ আপডেটের জন্য
        setTimeout(() => location.reload(), 500);
    })
    .catch(() => location.reload());
}
</script>