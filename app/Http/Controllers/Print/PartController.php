<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;

class PartController extends Controller
{
    public function index()
    {
        return view('print.part.index');
    }
}
