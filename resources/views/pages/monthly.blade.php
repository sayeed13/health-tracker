<x-app-layout title="মাসিক প্রগ্রেস">

<div class="mb-4">
    <h2 class="text-lg font-bold text-white">প্রগ্রেস চার্ট</h2>
    <p class="text-xs text-gray-500 mt-1">আপনার অগ্রগতি একনজরে</p>
</div>

{{-- Filter Tabs --}}
<div class="flex gap-2 mb-4">
    <button onclick="setFilter('week')"
            class="filter-btn flex-1 py-2.5 rounded-xl text-sm font-semibold transition"
            data-filter="week">
        সপ্তাহ
    </button>
    <button onclick="setFilter('month')"
            class="filter-btn flex-1 py-2.5 rounded-xl text-sm font-semibold transition active"
            data-filter="month">
        মাস
    </button>
    <button onclick="setFilter('year')"
            class="filter-btn flex-1 py-2.5 rounded-xl text-sm font-semibold transition"
            data-filter="year">
        বছর
    </button>
</div>

{{-- Month/Year Selector (শুধু month ফিল্টারে দেখাবে) --}}
<div id="monthSelector" class="bg-gray-900 border border-gray-800 rounded-xl p-3 mb-4">
    <div class="flex items-center justify-between">
        <button onclick="changeMonth(-1)"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-gray-800 text-gray-400 hover:text-white transition">
            ←
        </button>
        <span id="selectedMonth" class="text-sm font-semibold text-white">মে ২০২৬</span>
        <button onclick="changeMonth(1)"
                class="w-8 h-8 flex items-center justify-center rounded-lg
                       bg-gray-800 text-gray-400 hover:text-white transition">
            →
        </button>
    </div>
</div>

{{-- Loading --}}
<div id="loading" class="text-center py-12">
    <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent
                rounded-full animate-spin mx-auto mb-3"></div>
    <p class="text-gray-500 text-sm">লোড হচ্ছে...</p>
</div>

{{-- Charts Container --}}
<div id="chartsContainer" class="hidden space-y-4">

    {{-- ঔষধ Chart --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            ঔষধ
        </h3>
        <canvas id="medicineChart" height="200"></canvas>
    </div>

    {{-- নামাজ Chart --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-purple-500"></span>
            নামাজ
        </h3>
        <canvas id="prayerChart" height="200"></canvas>
    </div>

    {{-- ব্যায়াম Chart --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            ব্যায়াম
        </h3>
        <canvas id="exerciseChart" height="200"></canvas>
    </div>

    {{-- ধূমপান Chart --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            ধূমপান
        </h3>
        <canvas id="smokingChart" height="200"></canvas>
    </div>

</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
let currentFilter = 'month';
let currentYear   = new Date().getFullYear();
let currentMonth  = new Date().getMonth() + 1;

const charts = {
    medicine: null,
    prayer: null,
    exercise: null,
    smoking: null
};

const bnMonths = [
    'জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
    'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর'
];

async function loadStats() {
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('chartsContainer').classList.add('hidden');

    try {
        const url = `/monthly/stats?filter=${currentFilter}&year=${currentYear}&month=${currentMonth}`;
        const res = await fetch(url, {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();

        if (data.success) {
            renderCharts(data);
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('chartsContainer').classList.remove('hidden');
        }
    } catch (e) {
        console.error(e);
        document.getElementById('loading').innerHTML =
            '<p class="text-red-400 text-sm">লোড করতে সমস্যা হয়েছে</p>';
    }
}

function renderCharts(data) {
    const { labels, chart_data } = data;

    // Month selector visibility
    const selector = document.getElementById('monthSelector');
    if (currentFilter === 'month') {
        selector.classList.remove('hidden');
        document.getElementById('selectedMonth').textContent =
            `${bnMonths[currentMonth - 1]} ${currentYear}`;
    } else {
        selector.classList.add('hidden');
    }

    // Destroy old charts
    Object.keys(charts).forEach(key => {
        if (charts[key]) {
            charts[key].destroy();
        }
    });

    // Create new charts
    const chartConfig = (categoryKey) => ({
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: chart_data[categoryKey].label,
                data: chart_data[categoryKey].data,
                backgroundColor: chart_data[categoryKey].color,
                borderRadius: 6,
                barThickness: currentFilter === 'month' ? 8 : 20,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#d1d5db',
                    borderColor: '#374151',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: { display: false, color: '#374151' },
                    ticks: { color: '#9ca3af', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#374151' },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        stepSize: 1
                    }
                }
            }
        }
    });

    charts.medicine = new Chart(
        document.getElementById('medicineChart').getContext('2d'),
        chartConfig('medicine')
    );

    charts.prayer = new Chart(
        document.getElementById('prayerChart').getContext('2d'),
        chartConfig('prayer')
    );

    charts.exercise = new Chart(
        document.getElementById('exerciseChart').getContext('2d'),
        chartConfig('exercise')
    );

    charts.smoking = new Chart(
        document.getElementById('smokingChart').getContext('2d'),
        chartConfig('smoking')
    );
}

function setFilter(filter) {
    currentFilter = filter;

    // Reset month/year when changing filter
    if (filter === 'week' || filter === 'year') {
        currentYear  = new Date().getFullYear();
        currentMonth = new Date().getMonth() + 1;
    }

    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(btn => {
        const isActive = btn.dataset.filter === filter;
        btn.className = `filter-btn flex-1 py-2.5 rounded-xl text-sm font-semibold transition ${
            isActive
                ? 'bg-emerald-500 text-white active'
                : 'bg-gray-800 border border-gray-700 text-gray-400'
        }`;
    });

    loadStats();
}

function changeMonth(direction) {
    currentMonth += direction;

    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    } else if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }

    loadStats();
}

// Initial load
loadStats();
</script>

</x-app-layout>