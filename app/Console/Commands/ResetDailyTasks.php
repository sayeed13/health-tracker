<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\DailyLog;
use App\Models\TaskDefinition;
use App\Models\User;
use Carbon\Carbon;

#[Signature('tasks:daily-reset')]
#[Description('প্রতিদিন midnight এ নতুন দিনের টাস্ক লগ তৈরি করে')]
class ResetDailyTasks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $today   = Carbon::now($user->timezone)->toDateString();
            $weekday = Carbon::now($user->timezone)->dayOfWeek;
            $daysSinceStart = $user->daysSinceArtStart();

            // আজকের জন্য active tasks খুঁজি
            $tasks = TaskDefinition::where('is_active', true)
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
                })
                ->get();

            // প্রতিটা task এর জন্য আজকের log তৈরি করি
            foreach ($tasks as $task) {
                DailyLog::firstOrCreate(
                    [
                        'user_id'            => $user->id,
                        'task_definition_id' => $task->id,
                        'log_date'           => $today,
                    ],
                    ['is_completed' => false]
                );
            }

            $this->info("✅ {$user->name} — {$tasks->count()}টি টাস্ক তৈরি হয়েছে ({$today})");
        }

        $this->info('🎉 Daily reset সম্পন্ন!');
    }
}
