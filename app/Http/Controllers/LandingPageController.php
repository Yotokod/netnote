<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Feature;

class LandingPageController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->ordered()->with('features')->get();
        $features = Feature::active()->ordered()->get();
        
        return view('landing.index', compact('plans', 'features'));
    }
    
    public function pricing()
    {
        $plans = Plan::active()->ordered()->with('features')->get();
        
        return view('landing.pricing', compact('plans'));
    }
}
