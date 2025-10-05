<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Payment;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Liste des élèves avec pagination et filtres
     */
    public function index(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $class_id = $request->get('class_id');
            $series_id = $request->get('series_id');
            $gender = $request->get('gender');
            $status = $request->get('status');

            $query = Student::with(['class', 'series', 'payments' => function ($q) {
                $q->where('status', 'completed')->latest()->take(3);
            }])
            ->where('school_id', $school->id);

            // Recherche
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('matricule', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filtres
            if ($class_id) {
                $query->where('class_id', $class_id);
            }

            if ($series_id) {
                $query->where('series_id', $series_id);
            }

            if ($gender) {
                $query->where('gender', $gender);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $students = $query->orderBy('name')->paginate($perPage);

            // Ajouter des informations supplémentaires pour chaque élève
            $students->getCollection()->transform(function ($student) {
                $student->total_paid = $student->payments->sum('amount');
                $student->has_unpaid_fees = $student->fees()->where('status', '!=', 'paid')->exists();
                $student->average_grade = $student->grades()->avg('score') ?? 0;
                return $student;
            });

            return response()->json([
                'success' => true,
                'data' => $students,
                'meta' => [
                    'total' => $students->total(),
                    'per_page' => $students->perPage(),
                    'current_page' => $students->currentPage(),
                    'last_page' => $students->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des élèves',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Afficher un élève spécifique
     */
    public function show($id)
    {
        try {
            $student = Student::with([
                'school',
                'class',
                'series',
                'payments' => function ($q) {
                    $q->orderByDesc('created_at');
                },
                'grades' => function ($q) {
                    $q->with('subject', 'assessment')->orderByDesc('created_at');
                },
                'fees',
                'disciplinaryActions'
            ])->findOrFail($id);

            // Statistiques de l'élève
            $stats = [
                'total_payments' => $student->payments()->where('status', 'completed')->sum('amount'),
                'pending_payments' => $student->payments()->where('status', 'pending')->count(),
                'average_grade' => $student->grades()->avg('score') ?? 0,
                'total_grades' => $student->grades()->count(),
                'disciplinary_actions' => $student->disciplinaryActions()->count(),
                'attendance_rate' => 95 // À calculer selon votre système d'assiduité
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => $student,
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Élève non trouvé',
                'error' => config('app.debug') ? $e->getMessage() : 'Élève non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouvel élève
     */
    public function store(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'gender' => 'required|in:M,F',
                'nationality' => 'nullable|string|max:100',
                'religion' => 'nullable|string|max:100',
                'class_id' => 'required|exists:school_classes,id',
                'series_id' => 'nullable|exists:series,id',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'parents' => 'required|array|min:1',
                'parents.*.type' => 'required|in:father,mother,guardian',
                'parents.*.name' => 'required|string|max:255',
                'parents.*.phone1' => 'required|string|max:20',
                'parents.*.phone2' => 'nullable|string|max:20',
                'parents.*.email' => 'nullable|email|max:255',
                'parents.*.address' => 'nullable|string|max:500',
                'parents.*.profession' => 'nullable|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'medical_info' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Générer le matricule automatiquement
            $matricule = $this->generateMatricule($school);

            // Upload de la photo si fournie
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

            // Créer l'élève
            $student = Student::create([
                'name' => $request->name,
                'matricule' => $matricule,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'religion' => $request->religion,
                'school_id' => $school->id,
                'class_id' => $request->class_id,
                'series_id' => $request->series_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'photo_path' => $photoPath,
                'parents' => $request->parents,
                'medical_info' => $request->medical_info,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'status' => 'active',
                'enrollment_date' => Carbon::now()
            ]);

            DB::commit();

            $student->load(['class', 'series']);

            return response()->json([
                'success' => true,
                'message' => 'Élève créé avec succès',
                'data' => $student
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'élève',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour un élève
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'gender' => 'required|in:M,F',
                'nationality' => 'nullable|string|max:100',
                'religion' => 'nullable|string|max:100',
                'class_id' => 'required|exists:school_classes,id',
                'series_id' => 'nullable|exists:series,id',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'parents' => 'required|array|min:1',
                'parents.*.type' => 'required|in:father,mother,guardian',
                'parents.*.name' => 'required|string|max:255',
                'parents.*.phone1' => 'required|string|max:20',
                'parents.*.phone2' => 'nullable|string|max:20',
                'parents.*.email' => 'nullable|email|max:255',
                'parents.*.address' => 'nullable|string|max:500',
                'parents.*.profession' => 'nullable|string|max:255',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'medical_info' => 'nullable|string',
                'emergency_contact' => 'nullable|string|max:255',
                'emergency_phone' => 'nullable|string|max:20',
                'status' => 'required|in:active,suspended,expelled,graduated'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload de la nouvelle photo si fournie
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo
                if ($student->photo_path) {
                    Storage::disk('public')->delete($student->photo_path);
                }
                $photoPath = $request->file('photo')->store('students/photos', 'public');
                $student->photo_path = $photoPath;
            }

            // Mettre à jour les données
            $student->update([
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nationality' => $request->nationality,
                'religion' => $request->religion,
                'class_id' => $request->class_id,
                'series_id' => $request->series_id,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'parents' => $request->parents,
                'medical_info' => $request->medical_info,
                'emergency_contact' => $request->emergency_contact,
                'emergency_phone' => $request->emergency_phone,
                'status' => $request->status
            ]);

            $student->load(['class', 'series']);

            return response()->json([
                'success' => true,
                'message' => 'Élève mis à jour avec succès',
                'data' => $student
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'élève',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Supprimer un élève (soft delete)
     */
    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            
            // Changer le statut au lieu de supprimer
            $student->update(['status' => 'expelled']);

            return response()->json([
                'success' => true,
                'message' => 'Élève exclu avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'élève',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Assigner un élève à une classe
     */
    public function assignClassroom(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'class_id' => 'required|exists:school_classes,id',
                'series_id' => 'nullable|exists:series,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student->update([
                'class_id' => $request->class_id,
                'series_id' => $request->series_id
            ]);

            $student->load(['class', 'series']);

            return response()->json([
                'success' => true,
                'message' => 'Élève assigné à la classe avec succès',
                'data' => $student
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir le bulletin d'un élève
     */
    public function getBulletin($id)
    {
        try {
            $student = Student::with([
                'class',
                'series',
                'grades' => function ($q) {
                    $q->with(['subject', 'assessment'])
                      ->orderBy('created_at');
                }
            ])->findOrFail($id);

            // Calculer les moyennes par matière et générale
            $subjectAverages = $student->grades
                ->groupBy('subject_id')
                ->map(function ($grades, $subjectId) {
                    $subject = $grades->first()->subject;
                    return [
                        'subject' => $subject->name,
                        'grades' => $grades->pluck('score'),
                        'average' => round($grades->avg('score'), 2),
                        'coefficient' => $subject->coefficient ?? 1
                    ];
                });

            $generalAverage = $subjectAverages->isEmpty() ? 0 : 
                round($subjectAverages->avg('average'), 2);

            $bulletin = [
                'student' => $student,
                'subject_averages' => $subjectAverages->values(),
                'general_average' => $generalAverage,
                'rank' => $this->calculateRank($student, $generalAverage),
                'total_students' => Student::where('class_id', $student->class_id)->count(),
                'appreciation' => $this->getAppreciation($generalAverage)
            ];

            return response()->json([
                'success' => true,
                'data' => $bulletin
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du bulletin',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir les paiements d'un élève
     */
    public function getPayments($id)
    {
        try {
            $student = Student::findOrFail($id);
            
            $payments = $student->payments()
                ->with(['fee'])
                ->orderByDesc('created_at')
                ->paginate(15);

            $stats = [
                'total_paid' => $student->payments()->where('status', 'completed')->sum('amount'),
                'total_pending' => $student->payments()->where('status', 'pending')->sum('amount'),
                'total_failed' => $student->payments()->where('status', 'failed')->count(),
                'last_payment' => $student->payments()->where('status', 'completed')->latest()->first()
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => $payments,
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paiements',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Import en masse des élèves
     */
    public function bulkImport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:csv,xlsx|max:10240'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier invalide',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Ici vous pouvez implémenter la logique d'import
            // Utiliser Laravel Excel ou une autre librairie

            return response()->json([
                'success' => true,
                'message' => 'Import en cours de traitement',
                'data' => [
                    'job_id' => Str::uuid(),
                    'status' => 'processing'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Export des élèves
     */
    public function export(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            $format = $request->get('format', 'xlsx');

            // Ici vous pouvez implémenter la logique d'export
            // Utiliser Laravel Excel

            return response()->json([
                'success' => true,
                'message' => 'Export généré avec succès',
                'data' => [
                    'download_url' => url('/storage/exports/students_' . time() . '.' . $format)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    // ========== MÉTHODES UTILITAIRES ==========

    /**
     * Générer un matricule unique
     */
    private function generateMatricule(School $school)
    {
        $year = date('Y');
        $schoolCode = strtoupper(substr($school->name, 0, 3));
        
        do {
            $number = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $matricule = $schoolCode . $year . $number;
        } while (Student::where('matricule', $matricule)->exists());

        return $matricule;
    }

    /**
     * Calculer le rang de l'élève dans sa classe
     */
    private function calculateRank(Student $student, $average)
    {
        $betterStudents = Student::where('class_id', $student->class_id)
            ->where('id', '!=', $student->id)
            ->get()
            ->filter(function ($s) use ($average) {
                $avg = $s->grades()->avg('score') ?? 0;
                return $avg > $average;
            })
            ->count();

        return $betterStudents + 1;
    }

    /**
     * Obtenir l'appréciation selon la moyenne
     */
    private function getAppreciation($average)
    {
        if ($average >= 16) return 'Très Bien';
        if ($average >= 14) return 'Bien';
        if ($average >= 12) return 'Assez Bien';
        if ($average >= 10) return 'Passable';
        return 'Insuffisant';
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
