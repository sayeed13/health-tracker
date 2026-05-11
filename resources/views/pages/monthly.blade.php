<x-app-layout title="মাসিক প্রগ্রেস">

<div class="mb-4">
    <h2 class="text-lg font-bold text-white">প্রগ্রেস রিপোর্ট</h2>
    <p class="text-xs text-gray-500 mt-1" id="dateRangeLabel">লোড হচ্ছে...</p>
</div>

{{-- Filter Tabs --}}
<div class="flex gap-2 mb-4" id="filterTabs">
    <button onclick="setFilter('week')"
            class="filter-btn flex-1 py-2 rounded-xl text-xs font-semibold border transition
                   bg-gray-800 border-gray-700 text-gray-400"
            data-filter="week">
        এই সপ্তাহ
    </button>
    <button onclick="setFilter('month')"
            class="filter-btn flex-1 py-2 rounded-xl text-xs font-semibold border transition
                   bg-emerald-500 border-emerald-500 text-white"
            data-filter="month">
        এই মাস
    </button>
    <button onclick="setFilter('year')"
            class="filter-btn flex-1 py-2 rounded-xl text-xs font-semibold border transition
                   bg-gray-800 border-gray-700 text-gray-400"
            data-filter="year">
        এই বছর
    </button>
</div>

{{-- Loading --}}
<div id="loadingState" class="text-center py-12">
    <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent
                rounded-full animate-spin mx-auto mb-3"></div>
    <p class="text-gray-500 text-sm">লোড হচ্ছে...</p>
</div>

{{-- Content --}}
<div id="statsContent" class="hidden">

    {{-- ART Info --}}
    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-4 flex gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-emerald-400" id="artDay">-</div>
            <div class="text-xs text-emerald-700">ART দিন</div>
        </div>
        <div class="w-px bg-emerald-500/20"></div>
        <div class="text-center">
            <div class="text-2xl font-bold text-emerald-400" id="artStreak">-</div>
            <div class="text-xs text-emerald-700">ধারাবাহিক দিন</div>
        </div>
        <div class="flex-1 flex items-center">
            <p class="text-xs text-gray-500">
                প্রতিদিন ART নেওয়া মানে ড্রাগ রেজিস্ট্যান্সের ঝুঁকি শূন্য
            </p>
        </div>
    </div>

    {{-- Category Stats --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
        ক্যাটাগরি অনুযায়ী
    </h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4" id="categoryStats">
    </div>

    {{-- Heatmap --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
        দৈনিক অগ্রগতি
    </h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        <div id="heatmap" class="flex flex-wrap gap-1.5"></div>
        <div class="flex gap-3 mt-3">
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-emerald-500/80"></div>
                <span class="text-xs text-gray-500">দারুণ (৭০%+)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-emerald-500/30"></div>
                <span class="text-xs text-gray-500">ভালো (৪০%+)</span>
            </div>
            <div class="flex items-center gap-1">
                <div class="w-3 h-3 rounded bg-amber-500/30"></div>
                <span class="text-xs text-gray-500">কম</span>
            </div>
        </div>
    </div>

    {{-- Smoking Trend --}}
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
        ধূমপান ট্র্যাকার
    </h3>
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4">
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold text-red-400" id="totalSmoked">-</div>
                <div class="text-xs text-gray-500 mt-1">মোট খেয়েছি</div>
            </div>
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-3 text-center">
                <div class="text-2xl font-bold text-emerald-400" id="totalAvoided">-</div>
                <div class="text-xs text-gray-500 mt-1">মোট এড়িয়েছি</div>
            </div>
        </div>
        {{-- Smoking bar chart --}}
        <div id="smokingBars" class="space-y-1"></div>
    </div>

</div>

<script>
let currentFilter = 'month';

async function loadStats(filter) {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('statsContent').classList.add('hidden');

    try {
        const res  = await fetch(`/monthly/stats?filter=${filter}`, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();

        if (data.success) {
            renderStats(data);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('statsContent').classList.remove('hidden');
        }
    } catch (e) {
        document.getElementById('loadingState').innerHTML =
            '<p class="text-red-400 text-sm">লোড করতে সমস্যা হয়েছে</p>';
    }
}

function renderStats(data) {
    const { summary, stats, daily_breakdown, smoking_trend } = data;

    // Date range label + days count
    document.getElementById('dateRangeLabel').textContent =
        `${summary.start_date} থেকে ${summary.end_date} (${summary.days} দিন)`;

    // ART info
    document.getElementById('artDay').textContent    = summary.art_day;
    document.getElementById('artStreak').textContent = summary.art_streak;

    // Category stats
    const categories = {
        medicine: { label: 'ঔষধ',    color: 'red',    unit: 'ডোজ' },
        prayer:   { label: 'নামাজ',  color: 'purple', unit: 'ওয়াক্ত' },
        exercise: { label: 'ব্যায়াম', color: 'blue',   unit: 'সেশন' },
        smoking:  { label: 'ধূমপান', color: 'amber',  unit: 'সিগারেট' },
    };

    const catHtml = Object.entries(categories).map(([key, cat]) => {
        const s    = stats[key] || { rate: 0, completed: 0, target: 0, avoided: 0 };
        const rate = s.rate;
        const colorMap = {
            red: '#ef4444', purple: '#a855f7',
            blue: '#3b82f6', amber: '#f59e0b'
        };

        // ধূমপানের জন্য বিশেষ ডিসপ্লে
        if (key === 'smoking') {
            return `
            <div class="mb-4 last:mb-0">
                <div class="flex justify-between mb-1.5">
                    <span class="text-sm text-white font-medium">${cat.label}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">এড়িয়েছি ${s.avoided}/${s.target}</span>
                        <span class="text-sm font-bold text-emerald-400">${rate}%</span>
                    </div>
                </div>
                <div class="h-2.5 bg-gray-800 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-700 bg-emerald-500"
                         style="width:${rate}%"></div>
                </div>
                <div class="flex justify-between mt-1.5 text-xs">
                    <span class="text-red-400">✗ খেয়েছি: ${s.completed}</span>
                    <span class="text-emerald-400">✓ এড়িয়েছি: ${s.avoided}</span>
                </div>
            </div>`;
        }

        // অন্য category
        return `
        <div class="mb-4 last:mb-0">
            <div class="flex justify-between mb-1.5">
                <span class="text-sm text-white font-medium">${cat.label}</span>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">${s.completed}/${s.target} ${cat.unit}</span>
                    <span class="text-sm font-bold" style="color:${colorMap[cat.color]}">${rate}%</span>
                </div>
            </div>
            <div class="h-2.5 bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width:${rate}%;background:${colorMap[cat.color]}"></div>
            </div>
            <div class="flex justify-between mt-1.5 text-xs text-gray-600">
                <span>টার্গেট: ${s.target} ${cat.unit}</span>
                <span>মিস: ${s.missed} ${cat.unit}</span>
            </div>
        </div>`;
    }).join('');

    document.getElementById('categoryStats').innerHTML = catHtml;

    // Heatmap
    const heatmap = document.getElementById('heatmap');
    heatmap.innerHTML = '';
    daily_breakdown.forEach(day => {
        const rate  = day.rate;
        const color = day.total === 0 ? 'bg-gray-800'
                    : rate >= 70 ? 'bg-emerald-500/80'
                    : rate >= 40 ? 'bg-emerald-500/30'
                    : 'bg-amber-500/30';

        const div   = document.createElement('div');
        div.className = `w-7 h-7 rounded-lg ${color} flex items-center justify-center
                         text-xs text-white/60 cursor-default`;
        div.title     = `${day.date}: ${rate}%`;
        div.textContent = new Date(day.date).getDate();
        heatmap.appendChild(div);
    });

    // Smoking summary
    let totalSmoked  = 0;
    let totalAvoided = 0;

    smoking_trend.forEach(d => {
        totalSmoked  += parseInt(d.smoked  || 0);
        totalAvoided += parseInt(d.avoided || 0);
    });

    document.getElementById('totalSmoked').textContent  = totalSmoked;
    document.getElementById('totalAvoided').textContent = totalAvoided;

    // Smoking bars — recent days
    const bars = document.getElementById('smokingBars');
    bars.innerHTML = '<p class="text-xs text-gray-600 mb-2">সাম্প্রতিক দিনগুলো</p>';
    const recent = smoking_trend.slice(-10); // শেষ ১০ দিন
    recent.forEach(d => {
        const total   = parseInt(d.total || 0);
        const avoided = parseInt(d.avoided || 0);
        const pct     = total > 0 ? Math.round((avoided / total) * 100) : 0;
        const date    = new Date(d.log_date);
        bars.innerHTML += `
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs text-gray-500 w-16 flex-shrink-0">
                ${date.getDate()}/${date.getMonth()+1}
            </span>
            <div class="flex-1 h-4 bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500/60 rounded-full transition-all"
                     style="width:${pct}%"></div>
            </div>
            <span class="text-xs text-emerald-400 w-10 text-right">${avoided}</span>
            <span class="text-xs text-red-400 w-10 text-right">${total - avoided}</span>
        </div>`;
    });
}
function setFilter(filter) {
    currentFilter = filter;

    document.querySelectorAll('.filter-btn').forEach(btn => {
        const isActive = btn.dataset.filter === filter;
        btn.className = `filter-btn flex-1 py-2 rounded-xl text-xs font-semibold border transition ${
            isActive
                ? 'bg-emerald-500 border-emerald-500 text-white'
                : 'bg-gray-800 border-gray-700 text-gray-400'
        }`;
    });

    loadStats(filter);
}

// Page load
loadStats('month');
</script>

</x-app-layout>