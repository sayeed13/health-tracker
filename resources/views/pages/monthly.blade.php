<x-app-layout title="মাসিক প্রগ্রেস">

<style>
.filter-tabs {
    display: flex; gap: 6px; margin-bottom: 1.25rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px; padding: 4px;
}
.tab-btn {
    flex: 1; padding: 7px 4px;
    border: none; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    cursor: pointer; color: #6b7280;
    background: transparent; transition: all 0.15s;
}
.tab-btn.active { background: #10b981; color: white; }
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px; margin-bottom: 1.25rem;
}
.chart-card {
    background: #111827;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px; padding: 12px;
}
.chart-card .cat-label { font-size: 12px; font-weight: 600; color: #f9fafb; margin-bottom: 2px; }
.month-selector { display: flex; align-items: center; gap: 8px; margin-bottom: 1rem; }
.month-selector select {
    font-size: 12px; padding: 5px 10px;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 8px; background: #1f2937; color: #f9fafb; outline: none;
}
</style>

{{-- Header --}}
<div class="mb-4">
    <h2 class="text-lg font-bold text-white">প্রগ্রেস রিপোর্ট</h2>
    <p class="text-xs text-gray-500 mt-1" id="dateLabel">লোড হচ্ছে...</p>
</div>

{{-- ART Info --}}
<div class="bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4 mb-4 flex gap-4">
    <div class="text-center min-w-[56px]">
        <div class="text-2xl font-bold text-emerald-400" id="artDay">-</div>
        <div class="text-xs text-emerald-700">ART দিন</div>
    </div>
    <div class="w-px bg-emerald-500/20"></div>
    <div class="text-center min-w-[56px]">
        <div class="text-2xl font-bold text-emerald-400" id="artStreak">-</div>
        <div class="text-xs text-emerald-700">ধারাবাহিক</div>
    </div>
    <p class="text-xs text-gray-500 flex items-center">
        প্রতিদিন ART নেওয়া মানে ড্রাগ রেজিস্ট্যান্সের ঝুঁকি শূন্য
    </p>
</div>

{{-- Filter Tabs --}}
<div class="filter-tabs">
    <button class="tab-btn active" data-tab="week"  onclick="switchTab('week')">সাপ্তাহিক</button>
    <button class="tab-btn"        data-tab="month" onclick="switchTab('month')">মাসিক</button>
    <button class="tab-btn"        data-tab="year"  onclick="switchTab('year')">বার্ষিক</button>
</div>

{{-- Loading --}}
<div id="loadingState" class="text-center py-12">
    <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
    <p class="text-gray-500 text-sm">লোড হচ্ছে...</p>
</div>

{{-- Week Panel --}}
<div id="panelWeek" class="hidden">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">এই সপ্তাহের প্রতিদিনের তথ্য</p>
    <div class="charts-grid" id="weekCharts"></div>
</div>

{{-- Month Panel --}}
<div id="panelMonth" class="hidden">
    <div class="month-selector">
        <span class="text-xs text-gray-400">মাস বেছে নিন:</span>
        <select id="monthSelect" onchange="loadMonth()"></select>
    </div>
    <div class="charts-grid" id="monthCharts"></div>
</div>

{{-- Year Panel --}}
<div id="panelYear" class="hidden">
    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">বছরের প্রতি মাসের মোট</p>
    <div class="charts-grid" id="yearCharts"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<script>
const MONTHS_BN = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];

// smoking এ field='missed' কারণ — missed = এড়ানো (is_completed=0)
const CATS = {
    medicine: { label: 'ওষুধ',          color: '#E24B4A', field: 'completed' },
    exercise: { label: 'ব্যায়াম',        color: '#1D9E75', field: 'completed' },
    prayer:   { label: 'নামাজ',          color: '#7F77DD', field: 'completed' },
    smoking:  { label: 'ধূমপান এড়ানো', color: '#EF9F27', field: 'missed'    },
};

let activeCharts = {};

// ─── API ──────────────────────────────────────────────────────

async function fetchStats(filter, extra = {}) {
    const params = new URLSearchParams({ filter, ...extra });
    const res    = await fetch(`/monthly/stats?${params}`, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    });
    return res.json();
}

// ─── TAB SWITCH ───────────────────────────────────────────────

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b =>
        b.classList.toggle('active', b.dataset.tab === tab)
    );
    ['week','month','year'].forEach(t =>
        document.getElementById('panel' + cap(t)).classList.add('hidden')
    );
    document.getElementById('panel' + cap(tab)).classList.remove('hidden');

    if (tab === 'week')  loadWeek();
    if (tab === 'month') loadMonth();
    if (tab === 'year')  loadYear();
}

const cap = s => s.charAt(0).toUpperCase() + s.slice(1);
const pad = n => String(n).padStart(2, '0');

// ─── LOAD ─────────────────────────────────────────────────────

async function loadWeek() {
    showLoading(true);
    const data = await fetchStats('week').catch(() => null);
    if (data?.success) {
        updateART(data.summary);
        renderCharts('week', data.category_daily, weekLabels(), weekDates());
        document.getElementById('dateLabel').textContent =
            `${data.summary.start_date} থেকে ${data.summary.end_date}`;
    }
    showLoading(false);
}

async function loadMonth() {
    const month = parseInt(document.getElementById('monthSelect').value);
    const year  = new Date().getFullYear();
    const start = `${year}-${pad(month)}-01`;
    const end   = `${year}-${pad(month)}-${pad(new Date(year, month, 0).getDate())}`;

    showLoading(true);
    const data = await fetchStats('custom', { start, end }).catch(() => null);
    if (data?.success) {
        const days   = new Date(year, month, 0).getDate();
        const labels = Array.from({length: days}, (_, i) => `${i + 1}`);
        const dates  = Array.from({length: days}, (_, i) => `${year}-${pad(month)}-${pad(i + 1)}`);
        renderCharts('month', data.category_daily, labels, dates);
        document.getElementById('dateLabel').textContent =
            `${MONTHS_BN[month - 1]} ${year} — মাসিক ডেটা`;
    }
    showLoading(false);
}

async function loadYear() {
    showLoading(true);
    const data = await fetchStats('year').catch(() => null);
    if (data?.success) {
        const year         = new Date().getFullYear();
        const labels       = MONTHS_BN.map(m => m.slice(0, 3));
        const monthPrefixes = Array.from({length: 12}, (_, m) => `${year}-${pad(m + 1)}`);
        renderYearCharts(data.category_daily, labels, monthPrefixes);
        document.getElementById('dateLabel').textContent = `${year} সাল — বার্ষিক ডেটা`;
    }
    showLoading(false);
}

// ─── RENDER ───────────────────────────────────────────────────

function renderCharts(prefix, categoryDaily, labels, dates) {
    const container = document.getElementById(`${prefix}Charts`);
    container.innerHTML = '';
    destroyCharts(prefix);

    Object.entries(CATS).forEach(([cat, cfg]) => {
        const rows = categoryDaily[cat] || [];
        const vals = dates.map(date => {
            const row = rows.find(r => r.date === date);
            return row ? row[cfg.field] : 0;
        });
        buildCard(container, prefix, cat, cfg, labels, vals);
    });
}

function renderYearCharts(categoryDaily, labels, monthPrefixes) {
    const container = document.getElementById('yearCharts');
    container.innerHTML = '';
    destroyCharts('year');

    Object.entries(CATS).forEach(([cat, cfg]) => {
        const rows = categoryDaily[cat] || [];
        const vals = monthPrefixes.map(prefix =>
            rows.filter(r => r.date.startsWith(prefix))
                .reduce((sum, r) => sum + r[cfg.field], 0)
        );
        buildCard(container, 'year', cat, cfg, labels, vals);
    });
}

// ─── CHART BUILDER ────────────────────────────────────────────

function buildCard(container, prefix, cat, cfg, labels, vals) {
    const id   = `chart_${prefix}_${cat}`;
    const maxY = Math.max(...vals, 1) + 1;

    const card = document.createElement('div');
    card.className = 'chart-card';
    card.innerHTML = `
        <div class="cat-label">${cfg.label}</div>
        <div style="position:relative; height:120px;">
            <canvas id="${id}" role="img" aria-label="${cfg.label} বার চার্ট">${cfg.label}</canvas>
        </div>`;
    container.appendChild(card);

    setTimeout(() => {
        const ctx = document.getElementById(id);
        if (!ctx) return;
        if (activeCharts[id]) activeCharts[id].destroy();

        activeCharts[id] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: vals,
                    backgroundColor: cfg.color + '88',
                    borderColor:     cfg.color,
                    borderWidth:     1.5,
                    borderRadius:    3,
                    borderSkipped:   false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: c => `${c.parsed.y}` } }
                },
                scales: {
                    x: {
                        ticks: { font: { size: 8 }, color: '#6b7280', autoSkip: true, maxTicksLimit: 10, maxRotation: 0 },
                        grid: { display: false }, border: { display: false }
                    },
                    y: {
                        beginAtZero: true, max: maxY,
                        ticks: { font: { size: 8 }, color: '#6b7280', stepSize: Math.ceil(maxY / 3) },
                        grid: { color: 'rgba(255,255,255,0.04)' }, border: { display: false }
                    }
                }
            }
        });
    }, 30);
}

// ─── HELPERS ──────────────────────────────────────────────────

function weekMonday() {
    const today = new Date();
    const mon   = new Date(today);
    const dow   = today.getDay();
    mon.setDate(today.getDate() - (dow === 0 ? 6 : dow - 1));
    return mon;
}

function weekLabels() {
    const mon = weekMonday();
    return Array.from({length: 7}, (_, i) => {
        const d = new Date(mon); d.setDate(mon.getDate() + i);
        return `${d.getDate()}/${d.getMonth() + 1}`;
    });
}

function weekDates() {
    const mon = weekMonday();
    return Array.from({length: 7}, (_, i) => {
        const d = new Date(mon); d.setDate(mon.getDate() + i);
        return d.toISOString().slice(0, 10);
    });
}

function destroyCharts(prefix) {
    Object.keys(activeCharts).forEach(k => {
        if (k.startsWith(`chart_${prefix}_`)) {
            activeCharts[k].destroy();
            delete activeCharts[k];
        }
    });
}

function showLoading(show) {
    document.getElementById('loadingState').classList.toggle('hidden', !show);
}

function updateART(summary) {
    document.getElementById('artDay').textContent    = summary?.art_day    ?? '-';
    document.getElementById('artStreak').textContent = summary?.art_streak ?? '-';
}

function initMonthSelect() {
    const sel = document.getElementById('monthSelect');
    const cur = new Date().getMonth();
    MONTHS_BN.forEach((m, i) => {
        const opt       = document.createElement('option');
        opt.value       = i + 1;
        opt.textContent = m;
        if (i === cur) opt.selected = true;
        sel.appendChild(opt);
    });
}

// ─── INIT ─────────────────────────────────────────────────────

initMonthSelect();
loadWeek();
</script>

</x-app-layout>