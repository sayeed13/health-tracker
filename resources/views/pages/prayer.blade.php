<x-app-layout title="নামাজ">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">নামাজ</h2>
        <p class="text-xs text-gray-500 mt-1">
            {{ \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>

    <x-task-list
        :tasks="$tasks"
        :logs="$logs"
        route="prayer"
    />

    {{-- নামাজের সময় --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mt-4">
        <p class="text-xs text-gray-500 font-semibold mb-2 uppercase tracking-wider">নামাজের সময়সূচি</p>
        @php
            $prayers = [
                'ফজর'   => '৫:১৫',
                'জোহর'  => '১২:৩০',
                'আসর'   => '৪:৩০',
                'মাগরিব'=> '৭:০০',
                'এশা'   => '৯:০০',
            ];
        @endphp
        <div class="space-y-1">
            @foreach($prayers as $name => $time)
                <div class="flex justify-between">
                    <span class="text-xs text-gray-400">{{ $name }}</span>
                    <span class="text-xs text-gray-600">{{ $time }}</span>
                </div>
            @endforeach
        </div>
    </div>

</x-app-layout>