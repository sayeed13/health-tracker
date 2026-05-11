<?php

namespace App\Http\Controllers;

use App\Traits\HandlesTaskToggle;

class SmokingController
{
    use HandlesTaskToggle;

    public function index()
    {
        $data = $this->getTodayTasks('smoking');
        return view('pages.smoking', $data);
    }

    public function toggle(int $id)
    {
        return $this->toggleTask($id);
    }
}