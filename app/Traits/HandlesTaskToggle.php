<?php

namespace App\Traits;

use App\Models\DailyLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait HandlesTaskToggle
{
    public function toggleTask(int $taskId): JsonResponse
    {
        $user  = Auth::user();
        $today = Carbon::now($user->timezone)->toDateString();

        $log = DailyLog::firstOrCreate(
            [
                'user_id'            => $user->id,
                'task_definition_id' => $taskId,
                'log_date'           => $today,
            ],
            ['is_completed' => false]
        );

        $log->is_completed = !$log->is_completed;
        $log->completed_at = $log->is_completed ? now() : null;
        $log->save();

        return response()->json([
            'success'      => true,
            'is_completed' => $log->is_completed,
        ]);
    }

    protected function getTodayTasks(string $category): array
    {
        $user           = Auth::user();
        $today          = Carbon::now($user->timezone)->toDateString();
        $weekday        = Carbon::now($user->timezone)->dayOfWeek;
        $daysSinceStart = $user->daysSinceArtStart();

        $query = \App\Models\TaskDefinition::where('category', $category)
            ->where('is_active', true)
            ->where('active_from_day', '<=', $daysSinceStart)
            ->where(function ($q) use ($daysSinceStart) {
                $q->whereNull('active_until_day')
                ->orWhere('active_until_day', '>=', $daysSinceStart);
            })
            ->where(function ($q) use ($weekday) {
                $q->where('repeat_type', 'daily')
                ->orWhere(function ($q2) use ($weekday) {
                    $q2->where('repeat_type', 'weekly')
                        ->whereJsonContains('repeat_days', $weekday);
                });
            });

        if ($category === 'smoking') {
            $query->where('title', '!=', 'এক্সট্রা সিগারেট');
        }

        $tasks = $query->orderBy('sort_order')->get();
        
        $taskIds = $tasks->pluck('id');

        $logs = DailyLog::where('user_id', $user->id)
            ->where('log_date', $today)
            ->whereIn('task_definition_id', $taskIds)
            ->pluck('is_completed', 'task_definition_id');

        return [
            'tasks' => $tasks,
            'logs'  => $logs,
            'today' => $today,
            'user'  => $user,
        ];
    }
}