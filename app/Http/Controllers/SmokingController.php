<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\TaskDefinition;
use App\Traits\HandlesTaskToggle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmokingController
{
    use HandlesTaskToggle;

    public function index()
    {
        $data = $this->getTodayTasks('smoking');

        $user = Auth::user();
        $today = Carbon::now($user->timezone)->toDateString();

        $extraTask = TaskDefinition::where('category', 'smoking')
            ->where('title', 'এক্সট্রা সিগারেট')
            ->first();

        $extraCigarettes = 0;

        if ($extraTask) {
            $extraCigarettes = DailyLog::where('user_id', $user->id)
                ->where('task_definition_id', $extraTask->id)
                ->where('log_date', $today)
                ->value('quantity') ?? 0;
        }

        $data['extraCigarettes'] = $extraCigarettes;

        return view('pages.smoking', $data);
    }

    public function toggle(int $id)
    {
        return $this->toggleTask($id);
    }

    public function saveExtra(Request $request)
    {
        $request->validate([
            'extra_cigarettes' => ['required', 'integer', 'min:0'],
        ]);

        $user = Auth::user();
        $today = Carbon::now($user->timezone)->toDateString();

        $task = TaskDefinition::where('category', 'smoking')
            ->where('title', 'এক্সট্রা সিগারেট')
            ->firstOrFail();

        $count = (int) $request->extra_cigarettes;

        DailyLog::updateOrCreate(
            [
                'user_id' => $user->id,
                'task_definition_id' => $task->id,
                'log_date' => $today,
            ],
            [
                'is_completed' => $count > 0,
                'quantity' => $count,
                'unit' => 'cigarette',
                'completed_at' => $count > 0 ? now() : null,
            ]
        );

        return back()->with('success', 'এক্সট্রা সিগারেট count save হয়েছে।');
    }
}