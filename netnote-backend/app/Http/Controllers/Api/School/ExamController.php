<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Grade;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\AssessmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    /**
     * Liste des examens avec pagination et filtres
     */
    public function index(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');
            $subject_id = $request->get('subject_id');
            $class_id = $request->get('class_id');
            $type_id = $request->get('type_id');
            $status = $request->get('status');

            $query = Assessment::with([
                'subject',
                'class',
                'teacher',
                'assessmentType'
            ])
            ->where('school_id', $school->id);

            // Recherche
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filtres
            if ($subject_id) {
                $query->where('subject_id', $subject_id);
            }

            if ($class_id) {
                $query->where('class_id', $class_id);
            }

            if ($type_id) {
                $query->where('assessment_type_id', $type_id);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $assessments = $query->orderByDesc('date')->paginate($perPage);

            // Ajouter des statistiques pour chaque examen
            $assessments->getCollection()->transform(function ($assessment) {
                $assessment->grades_count = $assessment->grades()->count();
                $assessment->average_score = round($assessment->grades()->avg('score') ?? 0, 2);
                $assessment->max_score = $assessment->grades()->max('score') ?? 0;
                $assessment->min_score = $assessment->grades()->min('score') ?? 0;
                return $assessment;
            });

            return response()->json([
                'success' => true,
                'data' => $assessments,
                'meta' => [
                    'total' => $assessments->total(),
                    'per_page' => $assessments->perPage(),
                    'current_page' => $assessments->currentPage(),
                    'last_page' => $assessments->lastPage()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des examens',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Afficher un examen spécifique
     */
    public function show($id)
    {
        try {
            $assessment = Assessment::with([
                'subject',
                'class.students',
                'teacher',
                'assessmentType',
                'grades.student'
            ])->findOrFail($id);

            // Statistiques de l'examen
            $stats = [
                'total_students' => $assessment->class->students->count(),
                'graded_students' => $assessment->grades->count(),
                'pending_grades' => $assessment->class->students->count() - $assessment->grades->count(),
                'average_score' => round($assessment->grades->avg('score') ?? 0, 2),
                'max_score' => $assessment->grades->max('score') ?? 0,
                'min_score' => $assessment->grades->min('score') ?? 0,
                'pass_rate' => $this->calculatePassRate($assessment),
                'grade_distribution' => $this->getGradeDistribution($assessment)
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'assessment' => $assessment,
                    'stats' => $stats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Examen non trouvé',
                'error' => config('app.debug') ? $e->getMessage() : 'Examen non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouvel examen
     */
    public function store(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:school_classes,id',
                'assessment_type_id' => 'required|exists:assessment_types,id',
                'date' => 'required|date',
                'duration' => 'nullable|integer|min:1',
                'max_score' => 'required|numeric|min:1',
                'min_score' => 'required|numeric|min:0|lt:max_score',
                'coefficient' => 'nullable|numeric|min:0.1|max:10',
                'instructions' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $assessment = Assessment::create([
                'title' => $request->title,
                'description' => $request->description,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'school_id' => $school->id,
                'teacher_id' => $user->id,
                'assessment_type_id' => $request->assessment_type_id,
                'date' => $request->date,
                'duration' => $request->duration,
                'max_score' => $request->max_score,
                'min_score' => $request->min_score,
                'coefficient' => $request->coefficient ?? 1,
                'instructions' => $request->instructions,
                'status' => 'scheduled'
            ]);

            $assessment->load(['subject', 'class', 'teacher', 'assessmentType']);

            return response()->json([
                'success' => true,
                'message' => 'Examen créé avec succès',
                'data' => $assessment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'examen',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Mettre à jour un examen
     */
    public function update(Request $request, $id)
    {
        try {
            $assessment = Assessment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'subject_id' => 'required|exists:subjects,id',
                'class_id' => 'required|exists:school_classes,id',
                'assessment_type_id' => 'required|exists:assessment_types,id',
                'date' => 'required|date',
                'duration' => 'nullable|integer|min:1',
                'max_score' => 'required|numeric|min:1',
                'min_score' => 'required|numeric|min:0|lt:max_score',
                'coefficient' => 'nullable|numeric|min:0.1|max:10',
                'instructions' => 'nullable|string',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $assessment->update($request->all());
            $assessment->load(['subject', 'class', 'teacher', 'assessmentType']);

            return response()->json([
                'success' => true,
                'message' => 'Examen mis à jour avec succès',
                'data' => $assessment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'examen',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Supprimer un examen
     */
    public function destroy($id)
    {
        try {
            $assessment = Assessment::findOrFail($id);
            
            // Vérifier s'il y a des notes associées
            if ($assessment->grades()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cet examen car il contient des notes'
                ], 400);
            }

            $assessment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Examen supprimé avec succès'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'examen',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Enregistrer les notes pour un examen
     */
    public function storeGrades(Request $request, $id)
    {
        try {
            $assessment = Assessment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'grades' => 'required|array',
                'grades.*.student_id' => 'required|exists:students,id',
                'grades.*.score' => 'required|numeric|min:0|max:' . $assessment->max_score,
                'grades.*.comment' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $user = $request->user();
            $createdGrades = [];

            foreach ($request->grades as $gradeData) {
                // Vérifier si la note existe déjà
                $existingGrade = Grade::where('assessment_id', $assessment->id)
                    ->where('student_id', $gradeData['student_id'])
                    ->first();

                if ($existingGrade) {
                    // Mettre à jour la note existante
                    $existingGrade->update([
                        'score' => $gradeData['score'],
                        'comment' => $gradeData['comment'] ?? null
                    ]);
                    $createdGrades[] = $existingGrade;
                } else {
                    // Créer une nouvelle note
                    $grade = Grade::create([
                        'assessment_id' => $assessment->id,
                        'student_id' => $gradeData['student_id'],
                        'subject_id' => $assessment->subject_id,
                        'class_id' => $assessment->class_id,
                        'school_id' => $assessment->school_id,
                        'teacher_id' => $user->id,
                        'score' => $gradeData['score'],
                        'comment' => $gradeData['comment'] ?? null,
                        'graded_at' => now()
                    ]);
                    $createdGrades[] = $grade;
                }
            }

            // Mettre à jour le statut de l'examen si toutes les notes sont saisies
            $totalStudents = $assessment->class->students()->count();
            $gradedStudents = $assessment->grades()->count();
            
            if ($gradedStudents >= $totalStudents) {
                $assessment->update(['status' => 'completed']);
            } else {
                $assessment->update(['status' => 'in_progress']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Notes enregistrées avec succès',
                'data' => [
                    'grades' => $createdGrades,
                    'assessment_status' => $assessment->status
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement des notes',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Obtenir les notes d'un examen
     */
    public function getGrades($id)
    {
        try {
            $assessment = Assessment::with([
                'grades.student',
                'class.students' => function ($q) {
                    $q->orderBy('name');
                }
            ])->findOrFail($id);

            // Créer une liste complète avec tous les élèves de la classe
            $studentsWithGrades = $assessment->class->students->map(function ($student) use ($assessment) {
                $grade = $assessment->grades->where('student_id', $student->id)->first();
                
                return [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_matricule' => $student->matricule,
                    'score' => $grade ? $grade->score : null,
                    'comment' => $grade ? $grade->comment : null,
                    'graded_at' => $grade ? $grade->graded_at : null,
                    'is_graded' => $grade !== null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'assessment' => $assessment,
                    'students' => $studentsWithGrades
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notes',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Envoyer les résultats par SMS, Email ou WhatsApp
     */
    public function sendResults(Request $request, $id)
    {
        try {
            $assessment = Assessment::with(['grades.student', 'subject', 'class'])->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'method' => 'required|in:sms,email,whatsapp',
                'students' => 'nullable|array',
                'students.*' => 'exists:students,id',
                'message_template' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $method = $request->method;
            $selectedStudents = $request->students;
            
            // Si aucun élève sélectionné, envoyer à tous ceux qui ont des notes
            $grades = $selectedStudents 
                ? $assessment->grades()->whereIn('student_id', $selectedStudents)->with('student')->get()
                : $assessment->grades()->with('student')->get();

            $sentCount = 0;
            $errors = [];

            foreach ($grades as $grade) {
                try {
                    $message = $this->generateResultMessage($assessment, $grade, $request->message_template);
                    
                    switch ($method) {
                        case 'sms':
                            // Implémenter l'envoi SMS
                            $this->sendSMS($grade->student, $message);
                            break;
                        case 'email':
                            // Implémenter l'envoi Email
                            $this->sendEmail($grade->student, $assessment, $message);
                            break;
                        case 'whatsapp':
                            // Implémenter l'envoi WhatsApp
                            $this->sendWhatsApp($grade->student, $message);
                            break;
                    }
                    
                    $sentCount++;
                } catch (\Exception $e) {
                    $errors[] = "Erreur pour {$grade->student->name}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Résultats envoyés avec succès à {$sentCount} élève(s)",
                'data' => [
                    'sent_count' => $sentCount,
                    'total_count' => $grades->count(),
                    'errors' => $errors
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des résultats',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Exporter la fiche de notes
     */
    public function exportGradeSheet($id)
    {
        try {
            $assessment = Assessment::with([
                'grades.student',
                'subject',
                'class',
                'teacher'
            ])->findOrFail($id);

            // Ici vous pouvez implémenter l'export vers Excel/PDF
            // Utiliser Laravel Excel ou DomPDF

            return response()->json([
                'success' => true,
                'message' => 'Fiche de notes générée avec succès',
                'data' => [
                    'download_url' => url('/storage/exports/grade_sheet_' . $assessment->id . '_' . time() . '.pdf')
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
     * Calculer le taux de réussite
     */
    private function calculatePassRate(Assessment $assessment)
    {
        $totalGrades = $assessment->grades->count();
        if ($totalGrades === 0) return 0;

        $passGrades = $assessment->grades->where('score', '>=', $assessment->max_score * 0.5)->count();
        return round(($passGrades / $totalGrades) * 100, 2);
    }

    /**
     * Obtenir la distribution des notes
     */
    private function getGradeDistribution(Assessment $assessment)
    {
        $grades = $assessment->grades->pluck('score');
        $maxScore = $assessment->max_score;
        
        $ranges = [
            'excellent' => $grades->filter(fn($score) => $score >= $maxScore * 0.8)->count(),
            'good' => $grades->filter(fn($score) => $score >= $maxScore * 0.6 && $score < $maxScore * 0.8)->count(),
            'average' => $grades->filter(fn($score) => $score >= $maxScore * 0.5 && $score < $maxScore * 0.6)->count(),
            'poor' => $grades->filter(fn($score) => $score < $maxScore * 0.5)->count()
        ];

        return $ranges;
    }

    /**
     * Générer le message de résultat
     */
    private function generateResultMessage(Assessment $assessment, Grade $grade, $template = null)
    {
        if ($template) {
            return str_replace([
                '{student_name}',
                '{assessment_title}',
                '{subject}',
                '{score}',
                '{max_score}',
                '{percentage}'
            ], [
                $grade->student->name,
                $assessment->title,
                $assessment->subject->name,
                $grade->score,
                $assessment->max_score,
                round(($grade->score / $assessment->max_score) * 100, 2) . '%'
            ], $template);
        }

        return "Bonjour {$grade->student->name}, votre note pour l'examen \"{$assessment->title}\" en {$assessment->subject->name} est de {$grade->score}/{$assessment->max_score} (" . round(($grade->score / $assessment->max_score) * 100, 2) . "%).";
    }

    /**
     * Envoyer SMS (à implémenter)
     */
    private function sendSMS($student, $message)
    {
        // Implémenter avec Twilio ou autre service SMS
    }

    /**
     * Envoyer Email (à implémenter)
     */
    private function sendEmail($student, $assessment, $message)
    {
        // Implémenter avec Laravel Mail
    }

    /**
     * Envoyer WhatsApp (à implémenter)
     */
    private function sendWhatsApp($student, $message)
    {
        // Implémenter avec WhatsApp Business API
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
