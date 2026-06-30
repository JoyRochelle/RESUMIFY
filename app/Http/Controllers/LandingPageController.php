<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CvTemplate;

class LandingPageController extends Controller
{
    public function welcome()
    {
        return view('landing_page.welcome');
    }

    public function templates()
    {
        $templates = CvTemplate::where('is_active', true)->orderBy('sort_order')->get();
        return view('landing_page.templates', compact('templates'));
    }

    public function pricing()
    {
        return view('landing_page.pricing');
    }
}
