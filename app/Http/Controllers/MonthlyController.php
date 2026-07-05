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
        $filter = $request->get('filter', 'month'); // week, month, year
        $year   = $request->get('year', Carbon::now($user->timezone)->year);
        $month  = $request->get('month', Carbon::now($user->timezone)->month);

        [$startDate, $endDate, $labels] = $this->getDateRange($filter, $year, $month, $user->timezone);

        $categories = ['medicine', 'prayer', 'exercise', 'smoking'];
        $chartData  = [];

        foreach ($categories as $category) {
            $data = $this->getCategoryData($category, $startDate, $endDate, $labels, $user, $filter);
            $chartData[$category] = $data;
        }

        return response()->json([
            'success'    => true,
            'filter'     => $filter,
            'year'       => $year,
            'month'      => $month,
            'labels'     => $labels,
            'chart_data' => $chartData,
        ]);
    }

    private function getDateRange(string $filter, int $year, int $month, string $timezone): array
    {
        $carbon = Carbon::create($year, $month, 1, 0, 0, 0, $timezone);

        if ($filter === 'week') {
            // এই সপ্তাহের শুরু থেকে শেষ
            $start  = Carbon::now($timezone)->startOfWeek();
            $end    = Carbon::now($timezone)->endOfWeek();
            $labels = ['রবি', 'সোম', 'মঙ্গল', 'বুধ', 'বৃহ', 'শুক্র', 'শনি'];

        } elseif ($filter === 'month') {
            // নির্দিষ্ট মাসের শুরু থেকে শেষ
            $start  = $carbon->copy()->startOfMonth();
            $end    = $carbon->copy()->endOfMonth();
            $days   = $carbon->daysInMonth;
            $labels = range(1, $days); // [1, 2, 3, ..., 30/31]

        } else { // year
            // পুরো বছরের ১২ মাস
            $start  = Carbon::create($year, 1, 1, 0, 0, 0, $timezone);
            $end    = Carbon::create($year, 12, 31, 23, 59, 59, $timezone);
            $labels = ['জানু', 'ফেব', 'মার্চ', 'এপ্রিল', 'মে', 'জুন',
                       'জুলাই', 'আগস', 'সেপ্ট', 'অক্টো', 'নভে', 'ডিসে'];
        }

        return [$start->toDateString(), $end->toDateString(), $labels];
    }

    private function getCategoryData(string $category, string $start, string $end, array $labels, $user, string $filter): array
    {
        $data = [];

        if ($filter === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::parse($start, $user->timezone)->addDays($i)->toDateString();
                $data[] = $this->getMetricValueForDate($category, $date, $user);
            }
        } elseif ($filter === 'month') {
            $startCarbon = Carbon::parse($start, $user->timezone);
            $daysInMonth = $startCarbon->daysInMonth;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $startCarbon->copy()->setDay($day)->toDateString();
                $data[] = $this->getMetricValueForDate($category, $date, $user);
            }
        } else {
            $year = Carbon::parse($start)->year;

            for ($m = 1; $m <= 12; $m++) {
                $monthStart = Carbon::create($year, $m, 1, 0, 0, 0, $user->timezone)->startOfMonth()->toDateString();
                $monthEnd   = Carbon::create($year, $m, 1, 0, 0, 0, $user->timezone)->endOfMonth()->toDateString();

                $data[] = $this->getMetricValueForRange($category, $monthStart, $monthEnd, $user);
            }
        }

        return [
            'label' => $this->getCategoryLabel($category),
            'data'  => $data,
            'color' => $this->getCategoryColor($category),
        ];
    }

    private function getMetricValueForDate(string $category, string $date, $user): int
    {
        return match ($category) {
            'exercise' => $this->getExerciseValueForRange($date, $date, $user),
            'smoking'  => $this->getSmokingValueForRange($date, $date, $user),
            default    => $this->getDefaultCompletedCountForRange($category, $date, $date, $user),
        };
    }

    private function getMetricValueForRange(string $category, string $startDate, string $endDate, $user): int
    {
        return match ($category) {
            'exercise' => $this->getExerciseValueForRange($startDate, $endDate, $user),
            'smoking'  => $this->getSmokingValueForRange($startDate, $endDate, $user),
            default    => $this->getDefaultCompletedCountForRange($category, $startDate, $endDate, $user),
        };
    }

    private function getExerciseValueForRange(string $startDate, string $endDate, $user): int
    {
        return (int) DailyLog::where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->whereHas('taskDefinition', fn($q) => $q->where('category', 'exercise'))
            ->sum('quantity');
    }

    private function getSmokingValueForRange(string $startDate, string $endDate, $user): int
    {
        $plannedSmoked = DailyLog::where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->whereHas('taskDefinition', fn($q) =>
                $q->where('category', 'smoking')
                ->where('title', '!=', 'এক্সট্রা সিগারেট')
            )
            ->count();

        $extraSmoked = DailyLog::where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->whereHas('taskDefinition', fn($q) =>
                $q->where('category', 'smoking')
                ->where('title', 'এক্সট্রা সিগারেট')
            )
            ->sum('quantity');

        return (int) ($plannedSmoked + $extraSmoked);
    }

    private function getDefaultCompletedCountForRange(string $category, string $startDate, string $endDate, $user): int
    {
        return (int) DailyLog::where('user_id', $user->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->where('is_completed', true)
            ->whereHas('taskDefinition', fn($q) => $q->where('category', $category))
            ->count();
    }

    private function getCategoryLabel(string $category): string
    {
        return match($category) {
            'medicine' => 'ঔষধ',
            'prayer'   => 'নামাজ',
            'exercise' => 'ব্যায়াম',
            'smoking'  => 'ধূমপান',
            default    => $category,
        };
    }

    private function getCategoryColor(string $category): string
    {
        return match($category) {
            'medicine' => '#ef4444',  // red
            'prayer'   => '#a855f7',  // purple
            'exercise' => '#3b82f6',  // blue
            'smoking'  => '#f59e0b',  // amber
            default    => '#6b7280',
        };
    }
}