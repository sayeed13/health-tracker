<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\TaskDefinition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyController
{
    public function index()
    {
        return view('pages.monthly');
    }

    public function stats(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->get('filter', 'month');
        $user_tz = $user->timezone;

        [$startDate, $endDate, $days] = $this->getDateRange($filter, $request, $user_tz);

        // সব category র stats
        $categories = ['medicine', 'prayer', 'exercise', 'smoking'];

        $stats = [];
        foreach ($categories as $category) {

            // এই category তে দৈনিক কতটা টাস্ক আছে তা গণনা
            $tasksPerDay = $this->getTasksPerDay($category, $user);

            // টার্গেট = দৈনিক টাস্ক × দিনের সংখ্যা
            $targetTasks = $tasksPerDay * $days;

            // কতটা সম্পন্ন হয়েছে
            $completedTasks = DailyLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereHas('taskDefinition', fn($q) => $q->where('category', $category))
                ->whereBetween('log_date', [$startDate, $endDate])
                ->count();

            // ধূমপানের ক্ষেত্রে উল্টো — এড়ানোর হার
            if ($category === 'smoking') {
                $totalSmokingLogs = DailyLog::where('user_id', $user->id)
                    ->whereHas('taskDefinition', fn($q) => $q->where('category', 'smoking'))
                    ->whereBetween('log_date', [$startDate, $endDate])
                    ->count();

                $avoided = $totalSmokingLogs - $completedTasks;
                $rate    = $totalSmokingLogs > 0
                    ? round(($avoided / $totalSmokingLogs) * 100)
                    : 0;

                $stats[$category] = [
                    'target'    => $targetTasks,
                    'completed' => $completedTasks,
                    'avoided'   => $avoided,
                    'missed'    => $completedTasks,
                    'rate'      => $rate,
                ];
            } else {
                $rate = $targetTasks > 0
                    ? round(($completedTasks / $targetTasks) * 100)
                    : 0;

                $stats[$category] = [
                    'target'    => $targetTasks,
                    'completed' => $completedTasks,
                    'missed'    => $targetTasks - $completedTasks,
                    'rate'      => $rate,
                ];
            }
        }

        // দৈনিক breakdown — calendar heatmap এর জন্য
        $dailyBreakdown = DailyLog::where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->select(
                'log_date',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(is_completed) as completed')
            )
            ->groupBy('log_date')
            ->orderBy('log_date')
            ->get()
            ->map(fn($row) => [
                'date'      => $row->log_date,
                'total'     => $row->total,
                'completed' => $row->completed,
                'rate'      => $row->total > 0
                    ? round(($row->completed / $row->total) * 100)
                    : 0,
            ]);

        // ধূমপান trend
        $smokingTrend = DailyLog::where('user_id', $user->id)
            ->whereHas('taskDefinition', fn($q) => $q->where('category', 'smoking'))
            ->whereBetween('log_date', [$startDate, $endDate])
            ->select(
                'log_date',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(is_completed) as smoked'),
                DB::raw('SUM(CASE WHEN is_completed = 0 THEN 1 ELSE 0 END) as avoided')
            )
            ->groupBy('log_date')
            ->orderBy('log_date')
            ->get();

        // ART streak
        $artStreak = $this->calculateStreak($user);

        // সারসংক্ষেপ
        $summary = [
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'filter'      => $filter,
            'days'        => $days,
            'art_day'     => $user->daysSinceArtStart() + 1,
            'art_streak'  => $artStreak,
        ];

        return response()->json([
            'success'         => true,
            'summary'         => $summary,
            'stats'           => $stats,
            'daily_breakdown' => $dailyBreakdown,
            'smoking_trend'   => $smokingTrend,
        ]);
    }

    /**
     * প্রতিদিন এই category তে কতটা টাস্ক আছে
     */
    private function getTasksPerDay(string $category, $user): int
    {
        // daily টাস্ক
        $dailyCount = TaskDefinition::where('category', $category)
            ->where('repeat_type', 'daily')
            ->where('is_active', true)
            ->count();

        // weekly টাস্ক — এগুলো সপ্তাহে নির্দিষ্ট দিনে আসে
        // গড়ে দিনে কত হয় তা বের করি
        $weeklyTasks = TaskDefinition::where('category', $category)
            ->where('repeat_type', 'weekly')
            ->where('is_active', true)
            ->get();

        $weeklyPerDay = 0;
        foreach ($weeklyTasks as $task) {
            $daysPerWeek = count($task->repeat_days ?? []);
            $weeklyPerDay += $daysPerWeek / 7; // সপ্তাহে ৭ দিনে ভাগ
        }

        return (int) ceil($dailyCount + $weeklyPerDay);
    }

    private function getDateRange(string $filter, Request $request, string $timezone): array
    {
        $now = Carbon::now($timezone);

        $range = match($filter) {
            'week'  => [
                $now->copy()->startOfWeek()->toDateString(),
                $now->copy()->endOfWeek()->toDateString(),
            ],
            'month' => [
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
            ],
            'year'  => [
                $now->copy()->startOfYear()->toDateString(),
                $now->copy()->endOfYear()->toDateString(),
            ],
            'custom' => [
                $request->get('start', $now->copy()->startOfMonth()->toDateString()),
                $request->get('end', $now->copy()->toDateString()),
            ],
            default => [
                $now->copy()->startOfMonth()->toDateString(),
                $now->copy()->endOfMonth()->toDateString(),
            ],
        };

        // দিন গণনা
        $start = Carbon::parse($range[0], $timezone);
        $end   = Carbon::parse($range[1], $timezone);
        $days  = $start->diffInDays($end) + 1;

        return [$range[0], $range[1], $days];
    }

    private function calculateStreak($user): int
    {
        $streak = 0;
        $date   = Carbon::now($user->timezone)->subDay();

        while (true) {
            $taken = DailyLog::where('user_id', $user->id)
                ->where('log_date', $date->toDateString())
                ->where('is_completed', true)
                ->whereHas('taskDefinition', fn($q) => $q->where('category', 'medicine')
                    ->where('title', 'like', '%ART%'))
                ->exists();

            if (!$taken) break;

            $streak++;
            $date->subDay();
            if ($streak > 180) break;
        }

        return $streak;
    }
}