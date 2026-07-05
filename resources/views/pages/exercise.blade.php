<x-app-layout title="ব্যায়াম">

    <div class="mb-4">
        <h2 class="text-lg font-bold text-white">ব্যায়াম</h2>
        <p class="text-xs text-gray-500 mt-1">
            {{ \Carbon\Carbon::now('Asia/Colombo')->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}
        </p>
    </div>

    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-4">
        <p class="text-sm text-blue-300 font-medium">আজ কতটি Push-up করেছেন?</p>
        <p class="text-xs text-gray-500 mt-1">যতটা করেছেন, সংখ্যাটা লিখে সেভ করুন</p>
    </div>

    <form method="POST" action="{{ route('exercise.save') }}" class="bg-gray-900 border border-gray-800 rounded-xl p-4 space-y-4">
        @csrf

        <div>
            <label for="pushup_count" class="block text-sm font-medium text-white mb-2">
                Push-up Count
            </label>
            <input
                id="pushup_count"
                type="number"
                name="pushup_count"
                min="0"
                step="1"
                value="{{ old('pushup_count', $pushupCount) }}"
                class="w-full rounded-xl bg-gray-950 border border-gray-700 text-white px-4 py-3"
                placeholder="যেমন 10 / 20 / 30"
            >
            @error('pushup_count')
                <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-xl bg-blue-600 hover:bg-blue-500 text-white py-3 text-sm font-medium transition">
            সেভ করুন
        </button>
    </form>

    <div class="grid grid-cols-1 gap-3 mt-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $pushupCount }}</div>
            <div class="text-xs text-gray-500 mt-1">আজকের মোট Push-up</div>
        </div>
    </div>

</x-app-layout>