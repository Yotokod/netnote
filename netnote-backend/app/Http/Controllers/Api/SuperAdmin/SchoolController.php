<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    /**
     * Liste des écoles avec pagination et filtres
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status');
            $country_id = $request->get('country_id');
            $city_id = $request->get('city_id');

            $query = School::with(['country', 'city', 'template', 'bulletinTemplate'])
                ->withCount(['students', 'users', 'payments']);

            // Recherche
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('subdomain', 'like', "%{$search}%")
                      ->orWhere('email_pro', 'like', "%{$search}%");
                });
            }

            // Filtre par statut
            if ($status !== null) {
                $query->where('is_active', $status === 'active');
            }

            // Filtre par pays
            if ($country_id) {
                $query->where('country_id', $country_id);
            }

            // Filtre par ville
            if ($city_id) {
                $query->where('city_id', $city_id);
            }

            $schools = $query->orderByDesc('created_at')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $schools,
                'meta' => [
                    'total' => $schools->total(),
                    'per_page' => $schools->perPage(),
                    'current_page' => $schools->currentPage(),
                    'last_page' => $schools->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des écoles',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Afficher une école spécifique
     */
    public function show($id)
    {
        try {
            $school = School::with([
                'country', 
                'city', 
                'template', 
                'bulletinTemplate',
                'users' => function ($query) {
                    $query->withPivot('role_at_school', 'is_active');
                }
            ])
            ->withCount(['students', 'users', 'payments', 'classes'])
            ->findOrFail($id);

            // Statistiques de l'école
            $stats = $this->getSchoolStats($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'École non trouvée',
                'error' => config('app.debug') ? $e->getMessage() : 'École non trouvée'
            ], 404);
        }
    }

    /**
     * Créer une nouvelle école
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'subdomain' => 'required|string|max:50|unique:schools,subdomain|regex:/^[a-z0-9-]+$/',
                'custom_domain' => 'nullable|string|max:255|unique:schools,custom_domain',
                'founder' => 'nullable|string|max:255',
                'year_founded' => 'nullable|integer|min:1800|max:' . date('Y'),
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'quartier' => 'nullable|string|max:255',
                'phones' => 'nullable|array',
                'phones.*' => 'string|max:20',
                'email_pro' => 'nullable|email|max:255',
                'about' => 'nullable|string',
                'bibliography' => 'nullable|string',
                'template_id' => 'nullable|exists:templates,id',
                'bulletin_template_id' => 'nullable|exists:templates,id',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'admin_name' => 'required|string|max:255',
                'admin_email' => 'required|email|unique:users,email',
                'admin_phone' => 'nullable|string|max:20',
                'admin_password' => 'required|string|min:8',
                'settings' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload du logo si fourni
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('schools/logos', 'public');
            }

            // Créer l'école
            $school = School::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain,
                'logo_path' => $logoPath,
                'founder' => $request->founder,
                'year_founded' => $request->year_founded,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'quartier' => $request->quartier,
                'phones' => $request->phones,
                'email_pro' => $request->email_pro,
                'about' => $request->about,
                'bibliography' => $request->bibliography,
                'template_id' => $request->template_id,
                'bulletin_template_id' => $request->bulletin_template_id,
                'settings' => $request->settings ?? [],
                'is_active' => true
            ]);

            // Créer l'utilisateur admin
            $admin = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'phone' => $request->admin_phone,
                'password' => Hash::make($request->admin_password),
                'global_role' => 'admin',
                'is_active' => true
            ]);

            // Associer l'admin à l'école
            $school->users()->attach($admin->id, [
                'role_at_school' => 'admin',
                'is_active' => true
            ]);

            $school->load(['country', 'city', 'template', 'bulletinTemplate']);

            return response()->json([
                'success' => true,
                'message' => 'École créée avec succès',
                'data' => [
                    'school' => $school,
                    'admin' => $admin,
                    'subdomain_url' => 'https://' . $request->subdomain . '.' . config('app.domain')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour une école
     */
    public function update(Request $request, $id)
    {
        try {
            $school = School::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'subdomain' => 'required|string|max:50|unique:schools,subdomain,' . $id . '|regex:/^[a-z0-9-]+$/',
                'custom_domain' => 'nullable|string|max:255|unique:schools,custom_domain,' . $id,
                'founder' => 'nullable|string|max:255',
                'year_founded' => 'nullable|integer|min:1800|max:' . date('Y'),
                'country_id' => 'required|exists:countries,id',
                'city_id' => 'required|exists:cities,id',
                'quartier' => 'nullable|string|max:255',
                'phones' => 'nullable|array',
                'phones.*' => 'string|max:20',
                'email_pro' => 'nullable|email|max:255',
                'about' => 'nullable|string',
                'bibliography' => 'nullable|string',
                'template_id' => 'nullable|exists:templates,id',
                'bulletin_template_id' => 'nullable|exists:templates,id',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'settings' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload du nouveau logo si fourni
            if ($request->hasFile('logo')) {
                // Supprimer l'ancien logo
                if ($school->logo_path) {
                    Storage::disk('public')->delete($school->logo_path);
                }
                $logoPath = $request->file('logo')->store('schools/logos', 'public');
                $school->logo_path = $logoPath;
            }

            // Mettre à jour les données
            $school->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'subdomain' => $request->subdomain,
                'custom_domain' => $request->custom_domain,
                'founder' => $request->founder,
                'year_founded' => $request->year_founded,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'quartier' => $request->quartier,
                'phones' => $request->phones,
                'email_pro' => $request->email_pro,
                'about' => $request->about,
                'bibliography' => $request->bibliography,
                'template_id' => $request->template_id,
                'bulletin_template_id' => $request->bulletin_template_id,
                'settings' => array_merge($school->settings ?? [], $request->settings ?? [])
            ]);

            $school->load(['country', 'city', 'template', 'bulletinTemplate']);

            return response()->json([
                'success' => true,
                'message' => 'École mise à jour avec succès',
                'data' => $school
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Supprimer une école (soft delete)
     */
    public function destroy($id)
    {
        try {
            $school = School::findOrFail($id);
            
            // Désactiver l'école au lieu de la supprimer
            $school->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'École désactivée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Activer une école
     */
    public function activate($id)
    {
        try {
            $school = School::findOrFail($id);
            $school->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'École activée avec succès',
                'data' => $school
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'activation de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Désactiver une école
     */
    public function deactivate($id)
    {
        try {
            $school = School::findOrFail($id);
            $school->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'École désactivée avec succès',
                'data' => $school
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désactivation de l\'école',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Statistiques d'une école
     */
    public function getSchoolStats($id)
    {
        try {
            $school = School::findOrFail($id);

            $stats = [
                'students_count' => $school->students()->count(),
                'teachers_count' => $school->users()->where('global_role', 'teacher')->count(),
                'classes_count' => $school->classes()->count(),
                'subjects_count' => $school->subjects()->count(),
                'total_revenue' => $school->payments()->where('status', 'completed')->sum('amount'),
                'monthly_revenue' => $school->payments()
                    ->where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'pending_payments' => $school->payments()->where('status', 'pending')->count(),
                'active_users' => $school->users()->where('is_active', true)->count(),
                'recent_activities' => $school->students()
                    ->orderByDesc('created_at')
                    ->take(5)
                    ->get(['name', 'created_at'])
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir les données pour les filtres
     */
    public function getFiltersData()
    {
        try {
            $countries = Country::select('id', 'name')->orderBy('name')->get();
            $cities = City::select('id', 'name', 'country_id')->orderBy('name')->get();
            $templates = Template::where('type', 'landing')->select('id', 'name')->get();
            $bulletinTemplates = Template::where('type', 'bulletin')->select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'countries' => $countries,
                    'cities' => $cities,
                    'templates' => $templates,
                    'bulletin_templates' => $bulletinTemplates
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données de filtrage',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}
