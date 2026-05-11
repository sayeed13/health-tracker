<?php

namespace App\Http\Controllers;

use App\Traits\HandlesTaskToggle;

class ExerciseController
{
    use HandlesTaskToggle;

    public function index()
    {
        $data = $this->getTodayTasks('exercise');
        return view('pages.exercise', $data);
    }

    public function toggle(int $id)
    {
        return $this->toggleTask($id);
    }
}
