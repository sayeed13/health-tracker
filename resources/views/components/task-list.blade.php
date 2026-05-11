@props([
    'tasks',
    'logs',
    'route'        => 'medicine',
    'isSmokingPage'=> false,
])

@php
    $total     = $tasks->count();
    $completed = $logs->filter()->count();
    $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;

    // ধূমপান পেজে বেশি mark = বেশি খেয়েছি = খারাপ
    // তাই progress bar লাল এবং উল্টো হিসাব
    $progressPct   = $isSmokingPage ? $pct : $pct;
    $progressColor = $isSmokingPage
        ? ($pct === 0
            ? 'bg-emerald-500'          // কিছু খাইনি — সবুজ
            : ($pct <= 33
                ? 'bg-yellow-500'       // কম খেয়েছি — হলুদ
                : ($pct <= 66
                    ? 'bg-orange-500'   // মাঝামাঝি — কমলা
                    : 'bg-red-500')))   // বেশি খেয়েছি — লাল
        : 'bg-emerald-500';             // অন্য পেজ — সবুজ

    $progressLabel = $isSmokingPage
        ? ($pct === 0
            ? '🌟 আজকে একটাও খাননি!'
            : ($pct <= 33
                ? '👍 কম খেয়েছেন'
                : ($pct <= 66
                    ? '⚠️ মাঝামাঝি'
                    : '❗ বেশি খেয়েছেন')))
        : ($pct === 100
            ? '🎉 সব সম্পন্ন!'
            : ($pct >= 70
                ? '💪 দারুণ চলছে!'
                : ($pct >= 40
                    ? '⚡ চালিয়ে যান!'
                    : '🌟 শুরু করুন!')));
@endphp

{{-- Progress Card --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
    <div class="flex justify-between items-center mb-2">
        <span class="text-sm text-gray-400">আজকের অগ্রগতি</span>
        <span class="text-sm font-bold
                     {{ $isSmokingPage
                         ? ($pct === 0 ? 'text-emerald-400'
                           : ($pct <= 33 ? 'text-yellow-400'
                           : ($pct <= 66 ? 'text-orange-400'
                           : 'text-red-400')))
                         : 'text-emerald-400' }}">
            {{ $completed }}/{{ $total }}
        </span>
    </div>

    {{-- Progress Bar --}}
    <div class="h-3 bg-gray-800 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500 {{ $progressColor }}"
             style="width: {{ $pct }}%"></div>
    </div>

    {{-- Label --}}
    <div class="flex justify-between mt-2">
        <span class="text-xs text-gray-600">{{ $progressLabel }}</span>
        <span class="text-xs text-gray-600">{{ $pct }}%</span>
    </div>

    {{-- ধূমপান পেজে এড়ানোর হিসাব দেখাই --}}
    @if($isSmokingPage && $total > 0)
        <div class="mt-2 pt-2 border-t border-gray-800 flex justify-between text-xs">
            <span class="text-red-400">✗ খেয়েছি: {{ $completed }}</span>
            <span class="text-emerald-400">✓ এড়িয়েছি: {{ $total - $completed }}</span>
        </div>
    @endif
</div>

{{-- Task List --}}
<div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
    @forelse($tasks as $task)
        @php $isCompleted = $logs->get($task->id, false); @endphp
        <div class="flex items-center gap-3 px-4 py-4 transition active:bg-gray-800
                    {{ !$loop->last ? 'border-b border-gray-800' : '' }}"
             onclick="toggleTask({{ $task->id }}, '{{ $route }}')"
             style="cursor: pointer"
             id="task-row-{{ $task->id }}">

            {{-- Check Circle --}}
            <div id="check-{{ $task->id }}"
                 class="w-7 h-7 rounded-full border-2 flex items-center justify-center
                         flex-shrink-0 transition
                         {{ $isCompleted
                             ? ($isSmokingPage
                                 ? 'bg-red-500 border-red-500'
                                 : 'bg-emerald-500 border-emerald-500')
                             : 'border-gray-600 bg-transparent' }}">
                @if($isCompleted)
                    <svg class="w-3.5 h-3.5 text-white" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p id="title-{{ $task->id }}"
                   class="text-sm font-medium transition
                          {{ $isCompleted ? 'text-gray-500 line-through' : 'text-white' }}">
                    {{ $task->title }}
                </p>
                @if($task->description)
                    <p class="text-xs text-gray-600 mt-0.5">{{ $task->description }}</p>
                @endif
            </div>

            {{-- Time --}}
            @if($task->scheduled_time)
                <span class="text-xs text-gray-600 flex-shrink-0">
                    {{ \Carbon\Carbon::parse($task->scheduled_time)->format('h:i A') }}
                </span>
            @endif
        </div>
    @empty
        <div class="px-4 py-12 text-center">
            <p class="text-gray-600 text-sm">আজকে কোনো টাস্ক নেই</p>
        </div>
    @endforelse
</div>

{{-- Bottom Message --}}
<div class="mt-4 text-center">
    @if($isSmokingPage)
        @if($completed === 0 && $total > 0)
            <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4">
                <p class="text-emerald-400 text-sm font-semibold">
                    🌟 অসাধারণ! আজকে একটাও খাননি!
                </p>
            </div>
        @elseif($completed < $total)
            <div class="bg-yellow-500/5 border border-yellow-500/10 rounded-xl p-4">
                <p class="text-yellow-400 text-sm">
                    💪 {{ $total - $completed }}টি এড়াতে পেরেছেন!
                </p>
            </div>
        @endif
    @else
        @if($pct === 100)
            <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4">
                <p class="text-emerald-400 text-sm font-semibold">
                    🎉 অসাধারণ! আজকের সব সম্পন্ন!
                </p>
            </div>
        @endif
    @endif
</div>

<script>
const IS_SMOKING = {{ $isSmokingPage ? 'true' : 'false' }};

function toggleTask(taskId, route) {
    const check = document.getElementById('check-' + taskId);
    const title = document.getElementById('title-' + taskId);
    const isNowCompleted = !check.classList.contains('bg-emerald-500')
                        && !check.classList.contains('bg-red-500');

    // Optimistic UI
    if (isNowCompleted) {
        const doneColor = IS_SMOKING ? 'bg-red-500 border-red-500' : 'bg-emerald-500 border-emerald-500';
        check.className = `w-7 h-7 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition ${doneColor}`;
        check.innerHTML = `<svg class="w-3.5 h-3.5 text-white" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
        title.classList.add('text-gray-500', 'line-through');
        title.classList.remove('text-white');
    } else {
        check.className = 'w-7 h-7 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition border-gray-600 bg-transparent';
        check.innerHTML = '';
        title.classList.remove('text-gray-500', 'line-through');
        title.classList.add('text-white');
    }

    fetch(`/${route}/${taskId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(() => {
        // Progress bar রিফ্রেশ
        setTimeout(() => location.reload(), 300);
    })
    .catch(() => location.reload());
}
</script>