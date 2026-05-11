<?php

namespace App\Http\Controllers;

use App\Traits\HandlesTaskToggle;

class MedicineController
{
    use HandlesTaskToggle;

    public function index()
    {
        $data = $this->getTodayTasks('medicine');
        return view('pages.medicine', $data);
    }

    public function toggle(int $id)
    {
        return $this->toggleTask($id);
    }
}