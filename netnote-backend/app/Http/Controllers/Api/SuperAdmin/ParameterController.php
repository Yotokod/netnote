<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ParameterController extends Controller
{
    /**
     * Liste tous les paramètres système
     */
    public function index()
    {
        try {
            $data = [
                'countries' => Country::withCount('cities')->orderBy('name')->get(),
                'cities' => City::with('country')->orderBy('name')->get(),
                'religions' => $this->getConfigValues('religions'),
                'genders' => $this->getConfigValues('genders'),
                'nationalities' => $this->getConfigValues('nationalities'),
                'school_roles' => $this->getConfigValues('school_roles'),
                'class_types' => $this->getConfigValues('class_types'),
                'subject_types' => $this->getConfigValues('subject_types'),
                'student_statuses' => $this->getConfigValues('student_statuses'),
                'payment_statuses' => $this->getConfigValues('payment_statuses'),
                'school_levels' => $this->getConfigValues('school_levels'),
                'series' => $this->getConfigValues('series'),
                'trimesters' => $this->getConfigValues('trimesters'),
                'years' => range(date('Y') - 10, date('Y') + 5),
                'months' => [
                    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paramètres',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ========== PAYS ==========
    public function storeCountry(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:countries',
                'code' => 'required|string|max:3|unique:countries',
                'phone_code' => 'nullable|string|max:10',
                'currency' => 'nullable|string|max:10',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $country = Country::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pays créé avec succès',
                'data' => $country
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du pays',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    public function updateCountry(Request $request, Country $country)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
                'code' => 'required|string|max:3|unique:countries,code,' . $country->id,
                'phone_code' => 'nullable|string|max:10',
                'currency' => 'nullable|string|max:10',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $country->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pays mis à jour avec succès',
                'data' => $country
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du pays',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    public function deleteCountry(Country $country)
    {
        try {
            // Vérifier s'il y a des écoles associées
            if ($country->schools()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce pays car il contient des écoles'
                ], 400);
            }

            $country->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pays supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du pays',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ========== VILLES ==========
    public function storeCity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $city = City::create($request->all());
            $city->load('country');

            return response()->json([
                'success' => true,
                'message' => 'Ville créée avec succès',
                'data' => $city
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la ville',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    public function updateCity(Request $request, City $city)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'country_id' => 'required|exists:countries,id',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $city->update($request->all());
            $city->load('country');

            return response()->json([
                'success' => true,
                'message' => 'Ville mise à jour avec succès',
                'data' => $city
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la ville',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    public function deleteCity(City $city)
    {
        try {
            // Vérifier s'il y a des écoles associées
            if ($city->schools()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cette ville car elle contient des écoles'
                ], 400);
            }

            $city->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ville supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la ville',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ========== RELIGIONS ==========
    public function storeReligion(Request $request)
    {
        return $this->storeConfigValue('religions', $request);
    }

    public function updateReligion(Request $request, $id)
    {
        return $this->updateConfigValue('religions', $request, $id);
    }

    public function deleteReligion($id)
    {
        return $this->deleteConfigValue('religions', $id);
    }

    // ========== GENRES ==========
    public function storeGender(Request $request)
    {
        return $this->storeConfigValue('genders', $request);
    }

    public function updateGender(Request $request, $id)
    {
        return $this->updateConfigValue('genders', $request, $id);
    }

    public function deleteGender($id)
    {
        return $this->deleteConfigValue('genders', $id);
    }

    // ========== RÔLES ==========
    public function storeRole(Request $request)
    {
        return $this->storeConfigValue('school_roles', $request);
    }

    public function updateRole(Request $request, $id)
    {
        return $this->updateConfigValue('school_roles', $request, $id);
    }

    public function deleteRole($id)
    {
        return $this->deleteConfigValue('school_roles', $id);
    }

    // ========== TYPES DE CLASSES ==========
    public function storeClassType(Request $request)
    {
        return $this->storeConfigValue('class_types', $request);
    }

    public function updateClassType(Request $request, $id)
    {
        return $this->updateConfigValue('class_types', $request, $id);
    }

    public function deleteClassType($id)
    {
        return $this->deleteConfigValue('class_types', $id);
    }

    // ========== TYPES DE MATIÈRES ==========
    public function storeSubjectType(Request $request)
    {
        return $this->storeConfigValue('subject_types', $request);
    }

    public function updateSubjectType(Request $request, $id)
    {
        return $this->updateConfigValue('subject_types', $request, $id);
    }

    public function deleteSubjectType($id)
    {
        return $this->deleteConfigValue('subject_types', $id);
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Récupérer les valeurs de configuration depuis la base de données ou le fichier de config
     */
    private function getConfigValues($key)
    {
        // Pour l'instant, on utilise des valeurs par défaut
        // Plus tard, on pourra les stocker en base de données
        $defaults = [
            'religions' => [
                ['id' => 1, 'name' => 'Christianisme', 'is_active' => true],
                ['id' => 2, 'name' => 'Islam', 'is_active' => true],
                ['id' => 3, 'name' => 'Judaïsme', 'is_active' => true],
                ['id' => 4, 'name' => 'Bouddhisme', 'is_active' => true],
                ['id' => 5, 'name' => 'Autre', 'is_active' => true]
            ],
            'genders' => [
                ['id' => 1, 'name' => 'Masculin', 'code' => 'M', 'is_active' => true],
                ['id' => 2, 'name' => 'Féminin', 'code' => 'F', 'is_active' => true]
            ],
            'nationalities' => [
                ['id' => 1, 'name' => 'Béninoise', 'is_active' => true],
                ['id' => 2, 'name' => 'Togolaise', 'is_active' => true],
                ['id' => 3, 'name' => 'Nigérienne', 'is_active' => true],
                ['id' => 4, 'name' => 'Burkinabé', 'is_active' => true],
                ['id' => 5, 'name' => 'Ivoirienne', 'is_active' => true],
                ['id' => 6, 'name' => 'Française', 'is_active' => true]
            ],
            'school_roles' => [
                ['id' => 1, 'name' => 'Directeur', 'permissions' => ['all'], 'is_active' => true],
                ['id' => 2, 'name' => 'Directeur Adjoint', 'permissions' => ['manage_students', 'manage_teachers'], 'is_active' => true],
                ['id' => 3, 'name' => 'Comptable', 'permissions' => ['manage_finance'], 'is_active' => true],
                ['id' => 4, 'name' => 'Surveillant', 'permissions' => ['manage_discipline'], 'is_active' => true],
                ['id' => 5, 'name' => 'Surveillant Adjoint', 'permissions' => ['view_discipline'], 'is_active' => true],
                ['id' => 6, 'name' => 'Secrétaire', 'permissions' => ['manage_administration'], 'is_active' => true]
            ],
            'class_types' => [
                ['id' => 1, 'name' => 'Maternelle', 'is_active' => true],
                ['id' => 2, 'name' => 'Primaire', 'is_active' => true],
                ['id' => 3, 'name' => 'Collège', 'is_active' => true],
                ['id' => 4, 'name' => 'Lycée', 'is_active' => true]
            ],
            'subject_types' => [
                ['id' => 1, 'name' => 'Matière Principale', 'is_active' => true],
                ['id' => 2, 'name' => 'Matière Optionnelle', 'is_active' => true],
                ['id' => 3, 'name' => 'Sport', 'is_active' => true],
                ['id' => 4, 'name' => 'Art', 'is_active' => true]
            ],
            'student_statuses' => [
                ['id' => 1, 'name' => 'Actif', 'color' => 'success', 'is_active' => true],
                ['id' => 2, 'name' => 'Suspendu', 'color' => 'warning', 'is_active' => true],
                ['id' => 3, 'name' => 'Exclu', 'color' => 'danger', 'is_active' => true],
                ['id' => 4, 'name' => 'Diplômé', 'color' => 'info', 'is_active' => true]
            ],
            'payment_statuses' => [
                ['id' => 1, 'name' => 'En attente', 'color' => 'warning', 'is_active' => true],
                ['id' => 2, 'name' => 'Payé', 'color' => 'success', 'is_active' => true],
                ['id' => 3, 'name' => 'Échec', 'color' => 'danger', 'is_active' => true],
                ['id' => 4, 'name' => 'Remboursé', 'color' => 'info', 'is_active' => true]
            ],
            'school_levels' => [
                ['id' => 1, 'name' => 'CP', 'order' => 1, 'is_active' => true],
                ['id' => 2, 'name' => 'CE1', 'order' => 2, 'is_active' => true],
                ['id' => 3, 'name' => 'CE2', 'order' => 3, 'is_active' => true],
                ['id' => 4, 'name' => 'CM1', 'order' => 4, 'is_active' => true],
                ['id' => 5, 'name' => 'CM2', 'order' => 5, 'is_active' => true],
                ['id' => 6, 'name' => '6ème', 'order' => 6, 'is_active' => true],
                ['id' => 7, 'name' => '5ème', 'order' => 7, 'is_active' => true],
                ['id' => 8, 'name' => '4ème', 'order' => 8, 'is_active' => true],
                ['id' => 9, 'name' => '3ème', 'order' => 9, 'is_active' => true],
                ['id' => 10, 'name' => '2nde', 'order' => 10, 'is_active' => true],
                ['id' => 11, 'name' => '1ère', 'order' => 11, 'is_active' => true],
                ['id' => 12, 'name' => 'Terminale', 'order' => 12, 'is_active' => true]
            ],
            'series' => [
                ['id' => 1, 'name' => 'A', 'description' => 'Littéraire', 'is_active' => true],
                ['id' => 2, 'name' => 'C', 'description' => 'Scientifique', 'is_active' => true],
                ['id' => 3, 'name' => 'D', 'description' => 'Sciences Naturelles', 'is_active' => true],
                ['id' => 4, 'name' => 'E', 'description' => 'Sciences Techniques', 'is_active' => true],
                ['id' => 5, 'name' => 'F', 'description' => 'Techniques Industrielles', 'is_active' => true],
                ['id' => 6, 'name' => 'G', 'description' => 'Sciences Économiques', 'is_active' => true]
            ],
            'trimesters' => [
                ['id' => 1, 'name' => '1er Trimestre', 'start_month' => 9, 'end_month' => 12, 'is_active' => true],
                ['id' => 2, 'name' => '2ème Trimestre', 'start_month' => 1, 'end_month' => 4, 'is_active' => true],
                ['id' => 3, 'name' => '3ème Trimestre', 'start_month' => 4, 'end_month' => 7, 'is_active' => true]
            ]
        ];

        return $defaults[$key] ?? [];
    }

    /**
     * Ajouter une valeur de configuration
     */
    private function storeConfigValue($type, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'code' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:20',
                'permissions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ici, vous pourrez sauvegarder en base de données
            // Pour l'instant, on retourne juste un message de succès

            return response()->json([
                'success' => true,
                'message' => 'Paramètre créé avec succès',
                'data' => $request->all()
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du paramètre',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour une valeur de configuration
     */
    private function updateConfigValue($type, Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'code' => 'nullable|string|max:10',
                'color' => 'nullable|string|max:20',
                'permissions' => 'nullable|array',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ici, vous pourrez mettre à jour en base de données

            return response()->json([
                'success' => true,
                'message' => 'Paramètre mis à jour avec succès',
                'data' => array_merge($request->all(), ['id' => $id])
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du paramètre',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Supprimer une valeur de configuration
     */
    private function deleteConfigValue($type, $id)
    {
        try {
            // Ici, vous pourrez supprimer de la base de données

            return response()->json([
                'success' => true,
                'message' => 'Paramètre supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du paramètre',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}
