<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\LandingPageSection;

class WelcomeController extends Controller
{
    public function index()
    {
        $sections = LandingPageSection::all()->keyBy('key');

        return Inertia::render('Home/Home', [
            'canRegister' => Features::enabled(Features::registration()),
            'sections' => $sections,
        ]);
    }
}
