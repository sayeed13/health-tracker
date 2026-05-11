<?php

namespace App\Http\Controllers;

use App\Traits\HandlesTaskToggle;

class PrayerController
{
    use HandlesTaskToggle;

    public function index()
    {
        $data = $this->getTodayTasks('prayer');
        return view('pages.prayer', $data);
    }

    public function toggle(int $id)
    {
        return $this->toggleTask($id);
    }
}
