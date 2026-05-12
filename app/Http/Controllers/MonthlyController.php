<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyController extends Controller
{
    public function index()
    {
        return view('pages.monthly');
    }

    public function stats(Request $request)
    {
        $user    = Auth::user();
        $filter  = $request->get('filter', 'month');
        $user_tz = $user->timezone;

        [$startDate, $endDate, $days] = $this->getDateRange($filter, $request, $user_tz);

        $categories = ['medicine', 'exercise', 'prayer', 'smoking'];

        // ক্যাটাগরি ভিত্তিক মোট stats
        $stats = [];
        foreach ($categories as $category) {
            $completed = DailyLog::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereHas('taskDefinition', fn($q) => $q->where('category', $category))
                ->whereBetween('log_date', [$startDate, $endDate])
                ->count();

            if ($category === 'smoking') {
                $total   = DailyLog::where('user_id', $user->id)
                    ->whereHas('taskDefinition', fn($q) => $q->where('category', 'smoking'))
                    ->whereBetween('log_date', [$startDate, $endDate])
                    ->count();
                $avoided = $total - $completed;

                $stats[$category] = [
                    'total'     => $total,
                    'smoked'    => $completed,
                    'avoided'   => $avoided,
                    'rate'      => $total > 0 ? round(($avoided / $total) * 100) : 0,
                ];
            } else {
                $stats[$category] = [
                    'completed' => $completed,
                ];
            }
        }

        // ক্যাটাগরি ভিত্তিক দৈনিক breakdown (চার্টের মূল ডেটা)
        $categoryDaily = [];
        foreach ($categories as $category) {
            $rows = DailyLog::where('user_id', $user->id)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->whereHas('taskDefinition', fn($q) => $q->where('category', $category))
                ->select(
                    'log_date',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(is_completed) as completed'),
                    DB::raw('SUM(CASE WHEN is_completed = 0 THEN 1 ELSE 0 END) as missed')
                )
                ->groupBy('log_date')
                ->orderBy('log_date')
                ->get()
                ->map(fn($r) => [
                    'date'      => $r->log_date,
                    'total'     => (int) $r->total,
                    'completed' => (int) $r->completed,
                    'missed'    => (int) $r->missed,
                ]);

            $categoryDaily[$category] = $rows;
        }

        return response()->json([
            'success'        => true,
            'summary'        => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'filter'     => $filter,
                'days'       => $days,
                'art_day'    => $user->daysSinceArtStart() + 1,
                'art_streak' => $this->calculateStreak($user),
            ],
            'stats'          => $stats,
            'category_daily' => $categoryDaily,
        ]);
    }

    private function getDateRange(string $filter, Request $request, string $timezone): array
    {
        $now = Carbon::now($timezone);

        [$start, $end] = match ($filter) {
            'week'   => [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()],
            'month'  => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
            'year'   => [$now->copy()->startOfYear()->toDateString(), $now->copy()->endOfYear()->toDateString()],
            'custom' => [
                $request->get('start', $now->copy()->startOfMonth()->toDateString()),
                $request->get('end', $now->copy()->toDateString()),
            ],
            default  => [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()],
        };

        $days = Carbon::parse($start, $timezone)->diffInDays(Carbon::parse($end, $timezone)) + 1;

        return [$start, $end, $days];
    }

    private function calculateStreak($user): int
    {
        $streak = 0;
        $date   = Carbon::now($user->timezone)->subDay();

        while ($streak <= 180) {
            $taken = DailyLog::where('user_id', $user->id)
                ->where('log_date', $date->toDateString())
                ->where('is_completed', true)
                ->whereHas('taskDefinition', fn($q) => $q->where('category', 'medicine')->where('title', 'like', '%ART%'))
                ->exists();

            if (!$taken) break;

            $streak++;
            $date->subDay();
        }

        return $streak;
    }
}