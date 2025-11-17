<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplateController extends Controller
{
    public function Template () 
    {
        return Inertia::render('Dashboard/SelectTemplate', []);
    }

    public function EditorTemplate ()
    {
        return Inertia::render('Dashboard/Porfolios/Editor', []);
    }
}
