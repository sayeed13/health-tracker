<x-app-layout title="ঔষধ">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">ঔষধ</h2>
        <p class="text-xs text-gray-500 mt-1">
            আজকের তারিখ: {{ \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('D MMMM YYYY') }}
        </p>
    </div>

    {{-- ART Streak --}}
    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 mb-4 flex items-center gap-3">
        <div class="text-center">
            <div class="text-xl font-bold text-emerald-400">{{ $user->daysSinceArtStart() + 1 }}</div>
            <div class="text-xs text-emerald-700">দিন</div>
        </div>
        <div>
            <p class="text-sm text-emerald-400 font-medium">ART চলমান</p>
            <p class="text-xs text-gray-500">{{ $user->art_start_date->format('d M Y') }} থেকে শুরু</p>
        </div>
    </div>

    <x-task-list
        :tasks="$tasks"
        :logs="$logs"
        route="medicine"
    />

    {{-- reminder --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mt-4">
        <p class="text-xs text-gray-500 leading-relaxed">
            💊 <span class="text-gray-400">TB ওষুধ</span> — সকাল ১০:৩০, খালি পেটে<br>
            💊 <span class="text-gray-400">ART ওষুধ</span> — রাত ৮:০০, প্রতিদিন একই সময়ে
        </p>
    </div>

</x-app-layout>