<x-app-layout :title="'আজকের পরিকল্পনা'" :percentage="$percentage">

    {{-- স্ট্যাটস --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-400">{{ $completedTasks }}</div>
            <div class="text-xs text-gray-500 mt-1">সম্পন্ন</div>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-gray-400">{{ $totalTasks }}</div>
            <div class="text-xs text-gray-500 mt-1">মোট টাস্ক</div>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-emerald-400">{{ $percentage }}%</div>
            <div class="text-xs text-gray-500 mt-1">অগ্রগতি</div>
        </div>
    </div>

    {{-- ক্যাটাগরি লেবেল --}}
    @php
        $categoryLabels = [
            'medicine' => ['label' => 'ওষুধ',    'color' => 'text-red-400',    'bg' => 'bg-red-500/10    border-red-500/20'],
            'prayer'   => ['label' => 'নামাজ',   'color' => 'text-purple-400', 'bg' => 'bg-purple-500/10 border-purple-500/20'],
            'exercise' => ['label' => 'ব্যায়াম', 'color' => 'text-blue-400',  'bg' => 'bg-blue-500/10   border-blue-500/20'],
            'food'     => ['label' => 'খাবার',   'color' => 'text-amber-400',  'bg' => 'bg-amber-500/10  border-amber-500/20'],
            'water'    => ['label' => 'পানি',    'color' => 'text-cyan-400',   'bg' => 'bg-cyan-500/10   border-cyan-500/20'],
            'review'   => ['label' => 'রিভিউ',   'color' => 'text-gray-400',   'bg' => 'bg-gray-500/10   border-gray-500/20'],
        ];

        $sectionTitles = [
            'medicine' => 'ওষুধ',
            'prayer'   => 'নামাজ',
            'exercise' => 'ব্যায়াম',
            'food'     => 'খাবার',
            'water'    => 'পানি',
            'review'   => 'রিভিউ ও ঘুম',
        ];
    @endphp

    {{-- টাস্ক সেকশন --}}
    @foreach($tasksByCategory as $category => $categoryTasks)
        @if(isset($categoryLabels[$category]))
        <div class="mb-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                {{ $sectionTitles[$category] ?? $category }}
            </h3>
            <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                @foreach($categoryTasks as $index => $task)
                    @php $isCompleted = $logs->get($task->id, false); @endphp
                    <div class="flex items-center gap-3 px-4 py-3 cursor-pointer active:bg-gray-800 transition
                                {{ !$loop->last ? 'border-b border-gray-800' : '' }}"
                         onclick="toggleTask({{ $task->id }}, this)"
                         data-task-id="{{ $task->id }}">

                        {{-- চেক বোতাম --}}
                        <div id="check-{{ $task->id }}"
                             class="w-6 h-6 rounded-full border flex items-center justify-center flex-shrink-0 transition
                                    {{ $isCompleted
                                        ? 'bg-emerald-500 border-emerald-500'
                                        : 'border-gray-600' }}">
                            @if($isCompleted)
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            @endif
                        </div>

                        {{-- টাস্ক তথ্য --}}
                        <div class="flex-1 min-w-0">
                            <p id="text-{{ $task->id }}"
                               class="text-sm transition {{ $isCompleted ? 'text-gray-500 line-through' : 'text-white' }}">
                                {{ $task->title }}
                            </p>
                            @if($task->description)
                                <p class="text-xs text-gray-600 mt-0.5">{{ $task->description }}</p>
                            @endif
                        </div>

                        {{-- সময় ও ব্যাজ --}}
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            @if($task->scheduled_time)
                                <span class="text-xs text-gray-600">
                                    {{ \Carbon\Carbon::parse($task->scheduled_time)->format('h:i A') }}
                                </span>
                            @endif
                            <span class="text-xs px-2 py-0.5 rounded-full border
                                         {{ $categoryLabels[$category]['bg'] }}
                                         {{ $categoryLabels[$category]['color'] }}">
                                {{ $categoryLabels[$category]['label'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    {{-- উৎসাহমূলক বার্তা --}}
    <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4 text-center mt-4">
        @if($percentage === 100)
            <p class="text-emerald-400 text-sm font-semibold">🎉 অসাধারণ! আজকের সব টাস্ক সম্পন্ন!</p>
        @elseif($percentage >= 70)
            <p class="text-emerald-400 text-sm">💪 দারুণ চলছে! আরেকটু বাকি।</p>
        @elseif($percentage >= 40)
            <p class="text-amber-400 text-sm">⚡ ভালো শুরু। চালিয়ে যান!</p>
        @else
            <p class="text-gray-400 text-sm">🌟 আজকের যাত্রা শুরু হোক।</p>
        @endif
        <p class="text-gray-600 text-xs mt-1">ART এর {{ Auth::user()->daysSinceArtStart() + 1 }} তম দিন</p>
    </div>

</x-app-layout>

<script>
function toggleTask(taskId, element) {
    const check = document.getElementById('check-' + taskId);
    const text  = document.getElementById('text-' + taskId);
    const isCompleted = check.classList.contains('bg-emerald-500');

    // Optimistic UI update
    if (!isCompleted) {
        check.classList.add('bg-emerald-500', 'border-emerald-500');
        check.classList.remove('border-gray-600');
        check.innerHTML = `<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
        text.classList.add('text-gray-500', 'line-through');
        text.classList.remove('text-white');
    } else {
        check.classList.remove('bg-emerald-500', 'border-emerald-500');
        check.classList.add('border-gray-600');
        check.innerHTML = '';
        text.classList.remove('text-gray-500', 'line-through');
        text.classList.add('text-white');
    }

    // API call
    fetch(`/tasks/${taskId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(res => res.json())
    .then(data => {
        // Stats update
        location.reload();
    })
    .catch(() => {
        // Rollback on error
        location.reload();
    });
}
</script>