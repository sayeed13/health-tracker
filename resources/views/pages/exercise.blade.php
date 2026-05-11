<x-app-layout title="ব্যায়াম">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">ব্যায়াম</h2>
        <p class="text-xs text-gray-500 mt-1">
            {{ \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>

    @php
        $dayName = \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('dddd');
        $weekday = \Carbon\Carbon::now('Asia/Colombo')->dayOfWeek;
        $types   = [
            0 => ['type' => 'শক্তি বৃদ্ধি',   'color' => 'purple'],
            1 => ['type' => 'স্ট্রেচিং',       'color' => 'blue'],
            2 => ['type' => 'কার্ডিও',         'color' => 'emerald'],
            3 => ['type' => 'স্ট্রেচিং',       'color' => 'blue'],
            4 => ['type' => 'শক্তি বৃদ্ধি',   'color' => 'purple'],
            5 => ['type' => 'বিশ্রাম',         'color' => 'gray'],
            6 => ['type' => 'কার্ডিও',         'color' => 'emerald'],
        ];
        $todayType = $types[$weekday];
    @endphp

    {{-- আজকের ধরন --}}
    <div class="bg-{{ $todayType['color'] }}-500/10 border border-{{ $todayType['color'] }}-500/20
                rounded-xl p-3 mb-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-{{ $todayType['color'] }}-500/20 rounded-xl
                    flex items-center justify-center">
            <svg class="w-5 h-5 text-{{ $todayType['color'] }}-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-white">আজকের ধরন: {{ $todayType['type'] }}</p>
            <p class="text-xs text-gray-500">সকাল ৮:৩০ — ৩০ মিনিট</p>
        </div>
    </div>

    <x-task-list
        :tasks="$tasks"
        :logs="$logs"
        route="exercise"
    />

    @if($tasks->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 text-center">
            <p class="text-2xl mb-2">😴</p>
            <p class="text-gray-400 text-sm font-medium">আজকে বিশ্রামের দিন</p>
            <p class="text-gray-600 text-xs mt-1">শুক্রবার সম্পূর্ণ বিশ্রাম নিন</p>
        </div>
    @endif

</x-app-layout>