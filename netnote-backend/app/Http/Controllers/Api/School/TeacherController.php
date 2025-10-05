<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Teacher;
use App\Models\School;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Liste des professeurs avec pagination et filtres
     */
    public function index(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $subject_id = $request->get('subject_id');
            $class_id = $request->get('class_id');
            $status = $request->get('status');

            $query = User::with(['teacherProfile', 'schools' => function ($q) use ($school) {
                $q->where('school_id', $school->id);
            }])
            ->whereHas('schools', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })
            ->where('global_role', 'teacher');

            // Recherche
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filtre par matière
            if ($subject_id) {
                $query->whereHas('teacherProfile.subjects', function ($q) use ($subject_id) {
                    $q->where('subject_id', $subject_id);
                });
            }

            // Filtre par classe
            if ($class_id) {
                $query->whereHas('teacherProfile.classes', function ($q) use ($class_id) {
                    $q->where('school_class_id', $class_id);
                });
            }

            // Filtre par statut
            if ($status !== null) {
                $query->where('is_active', $status === 'active');
            }

            $teachers = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $teachers,
                'meta' => [
                    'total' => $teachers->total(),
                    'per_page' => $teachers->perPage(),
                    'current_page' => $teachers->currentPage(),
                    'last_page' => $teachers->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des professeurs',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Afficher un professeur spécifique
     */
    public function show($id)
    {
        try {
            $teacher = User::with([
                'teacherProfile.subjects',
                'teacherProfile.classes',
                'schools',
                'assessments' => function ($q) {
                    $q->with('subject', 'class')->latest()->take(5);
                },
                'grades' => function ($q) {
                    $q->with('student', 'subject')->latest()->take(10);
                }
            ])->findOrFail($id);

            // Statistiques du professeur
            $stats = [
                'total_students' => $teacher->teacherProfile?->classes()->withCount('students')->get()->sum('students_count') ?? 0,
                'total_subjects' => $teacher->teacherProfile?->subjects()->count() ?? 0,
                'total_classes' => $teacher->teacherProfile?->classes()->count() ?? 0,
                'total_assessments' => $teacher->assessments()->count(),
                'total_grades' => $teacher->grades()->count(),
                'average_grades_given' => round($teacher->grades()->avg('score') ?? 0, 2)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'teacher' => $teacher,
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Professeur non trouvé',
                'error' => config('app.debug') ? $e->getMessage() : 'Professeur non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouveau professeur
     */
    public function store(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:20',
                'phone2' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'password' => 'required|string|min:8',
                'specialization' => 'nullable|string|max:255',
                'qualification' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer|min:0',
                'hire_date' => 'nullable|date',
                'salary' => 'nullable|numeric|min:0',
                'subjects' => 'nullable|array',
                'subjects.*' => 'exists:subjects,id',
                'classes' => 'nullable|array',
                'classes.*' => 'exists:school_classes,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Créer l'utilisateur
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'global_role' => 'teacher',
                'is_active' => true,
                'profile_data' => [
                    'phone2' => $request->phone2,
                    'address' => $request->address
                ]
            ]);

            // Créer le profil professeur
            $teacherProfile = Teacher::create([
                'user_id' => $user->id,
                'school_id' => $school->id,
                'specialization' => $request->specialization,
                'qualification' => $request->qualification,
                'experience_years' => $request->experience_years,
                'hire_date' => $request->hire_date ?? now(),
                'salary' => $request->salary,
                'is_active' => true
            ]);

            // Associer le professeur à l'école
            $user->schools()->attach($school->id, [
                'role_at_school' => 'teacher',
                'is_active' => true
            ]);

            // Assigner les matières si fournies
            if ($request->subjects) {
                $teacherProfile->subjects()->attach($request->subjects);
            }

            // Assigner les classes si fournies
            if ($request->classes) {
                $teacherProfile->classes()->attach($request->classes);
            }

            DB::commit();

            $user->load(['teacherProfile.subjects', 'teacherProfile.classes']);

            return response()->json([
                'success' => true,
                'message' => 'Professeur créé avec succès',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du professeur',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour un professeur
     */
    public function update(Request $request, $id)
    {
        try {
            $teacher = User::with('teacherProfile')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
                'phone2' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'specialization' => 'nullable|string|max:255',
                'qualification' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer|min:0',
                'hire_date' => 'nullable|date',
                'salary' => 'nullable|numeric|min:0',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Mettre à jour l'utilisateur
            $teacher->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => $request->get('is_active', true),
                'profile_data' => array_merge($teacher->profile_data ?? [], [
                    'phone2' => $request->phone2,
                    'address' => $request->address
                ])
            ]);

            // Mettre à jour le profil professeur
            if ($teacher->teacherProfile) {
                $teacher->teacherProfile->update([
                    'specialization' => $request->specialization,
                    'qualification' => $request->qualification,
                    'experience_years' => $request->experience_years,
                    'hire_date' => $request->hire_date,
                    'salary' => $request->salary,
                    'is_active' => $request->get('is_active', true)
                ]);
            }

            DB::commit();

            $teacher->load(['teacherProfile.subjects', 'teacherProfile.classes']);

            return response()->json([
                'success' => true,
                'message' => 'Professeur mis à jour avec succès',
                'data' => $teacher
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du professeur',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Supprimer un professeur (désactiver)
     */
    public function destroy($id)
    {
        try {
            $teacher = User::findOrFail($id);
            
            $teacher->update(['is_active' => false]);
            
            if ($teacher->teacherProfile) {
                $teacher->teacherProfile->update(['is_active' => false]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Professeur désactivé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du professeur',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Assigner des matières à un professeur
     */
    public function assignSubjects(Request $request, $id)
    {
        try {
            $teacher = User::with('teacherProfile')->findOrFail($id);

            if (!$teacher->teacherProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil professeur non trouvé'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'subjects' => 'required|array',
                'subjects.*' => 'exists:subjects,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Synchroniser les matières
            $teacher->teacherProfile->subjects()->sync($request->subjects);

            $teacher->load(['teacherProfile.subjects']);

            return response()->json([
                'success' => true,
                'message' => 'Matières assignées avec succès',
                'data' => $teacher->teacherProfile->subjects
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation des matières',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir l'emploi du temps d'un professeur
     */
    public function getSchedule($id)
    {
        try {
            $teacher = User::findOrFail($id);

            $schedules = $teacher->schedules()
                ->with(['subject', 'class', 'school'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');

            return response()->json([
                'success' => true,
                'data' => $schedules
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'emploi du temps',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir les classes d'un professeur
     */
    public function getClasses($id)
    {
        try {
            $teacher = User::with(['teacherProfile.classes.students'])->findOrFail($id);

            if (!$teacher->teacherProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil professeur non trouvé'
                ], 404);
            }

            $classes = $teacher->teacherProfile->classes->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'students_count' => $class->students->count(),
                    'level' => $class->level,
                    'series' => $class->series
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $classes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des classes',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir les données pour les filtres
     */
    public function getFiltersData(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);

            $subjects = Subject::where('school_id', $school->id)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $classes = SchoolClass::where('school_id', $school->id)
                ->select('id', 'name', 'level')
                ->orderBy('level')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'subjects' => $subjects,
                    'classes' => $classes
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

    /**
     * Obtenir l'école courante
     */
    private function getCurrentSchool(Request $request)
    {
        $user = $request->user();
        return $user->isSuperAdmin() 
            ? School::find($request->get('school_id'))
            : $user->schools()->first();
    }
}
