<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index()
    {
        return view('print.task.index');
    }
}
