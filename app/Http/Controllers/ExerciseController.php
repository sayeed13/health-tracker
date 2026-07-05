<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\TaskDefinition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseController
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now($user->timezone)->toDateString();

        $task = TaskDefinition::where('category', 'exercise')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->first();

        $pushupCount = 0;

        if ($task) {
            $pushupCount = DailyLog::where('user_id', $user->id)
                ->where('task_definition_id', $task->id)
                ->where('log_date', $today)
                ->value('quantity') ?? 0;
        }

        return view('pages.exercise', [
            'user' => $user,
            'today' => $today,
            'pushupCount' => $pushupCount,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'pushup_count' => ['required', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $today = Carbon::now($user->timezone)->toDateString();

        $task = TaskDefinition::where('category', 'exercise')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->firstOrFail();

        $count = (int) $request->pushup_count;

        DailyLog::updateOrCreate(
            [
                'user_id' => $user->id,
                'task_definition_id' => $task->id,
                'log_date' => $today,
            ],
            [
                'is_completed' => $count > 0,
                'quantity' => $count,
                'unit' => 'pushup',
                'completed_at' => $count > 0 ? now() : null,
            ]
        );

        return back()->with('success', 'আজকের push-up count save হয়েছে।');
    }
}