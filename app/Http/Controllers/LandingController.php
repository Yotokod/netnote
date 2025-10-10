<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'schools_count' => School::active()->count(),
            'students_count' => 0, // À calculer plus tard
            'countries_count' => Country::count(),
        ];

        $plans = \App\Models\Plan::active()->ordered()->with('features')->get();
        $features = \App\Models\Feature::active()->ordered()->get();

        return view('landing.index', compact('stats', 'plans', 'features'));
    }

    public function schoolRegistration()
    {
        $countries = Country::with('cities')->get();
        
        return view('landing.school-registration', compact('countries'));
    }

    public function storeSchoolRegistration(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'founder' => 'required|string|max:255',
            'year_founded' => 'required|integer|min:1900|max:' . date('Y'),
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'quartier' => 'required|string|max:255',
            'email_pro' => 'required|email|unique:schools,email_pro',
            'phone' => 'required|string',
            'about' => 'nullable|string',
        ]);

        // Générer le sous-domaine
        $subdomain = $this->generateSubdomain($validated['name']);
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['subdomain'] = $subdomain;
        $validated['phones'] = [$validated['phone']];
        
        unset($validated['phone']);

        $school = School::create($validated);

        return redirect()->route('landing.registration-success')
            ->with('school', $school)
            ->with('success', 'École enregistrée avec succès !');
    }

    public function registrationSuccess()
    {
        return view('landing.registration-success');
    }

    protected function generateSubdomain(string $schoolName): string
    {
        $baseSubdomain = Str::slug($schoolName);
        $subdomain = $baseSubdomain;
        $counter = 1;

        while (School::where('subdomain', $subdomain)->exists()) {
            $subdomain = $baseSubdomain . '-' . $counter;
            $counter++;
        }

        return $subdomain;
    }
}