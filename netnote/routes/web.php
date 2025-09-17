<?php

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

// Landing page routes (domaine principal)
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/inscription-ecole', [LandingController::class, 'schoolRegistration'])->name('school.registration');
Route::post('/inscription-ecole', [LandingController::class, 'storeSchoolRegistration'])->name('school.registration.store');
Route::get('/inscription-reussie', [LandingController::class, 'registrationSuccess'])->name('landing.registration-success');

// Routes pour les sous-domaines d'écoles
Route::group(['middleware' => 'tenant'], function () {
    Route::get('/', function () {
        $tenant = app(\Spatie\Multitenancy\Models\Concerns\UsesTenantModel::class)->current();
        return view('school.dashboard', compact('tenant'));
    })->name('school.dashboard');
});
