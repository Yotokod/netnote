<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Payment;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard principal de l'école
     */
    public function index(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'École non trouvée'
                ], 404);
            }

            $stats = $this->getStats($school);
            $charts = $this->getCharts($school, $request);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'school' => $school,
                    'stats' => $stats,
                    'charts' => $charts
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du dashboard',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Statistiques de l'école
     */
    public function getStats(School $school)
    {
        try {
            $totalStudents = $school->students()->count();
            $maleStudents = $school->students()->where('gender', 'M')->count();
            $femaleStudents = $school->students()->where('gender', 'F')->count();
            $totalTeachers = $school->users()->where('global_role', 'teacher')->count();
            $totalClassrooms = $school->classes()->count();

            // Revenus du mois
            $monthlyRevenue = $school->payments()
                ->where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');

            // Paiements en attente
            $pendingPayments = $school->payments()
                ->where('status', 'pending')
                ->count();

            // Élèves avec frais non soldés
            $unpaidStudents = $school->students()
                ->whereHas('fees', function ($query) {
                    $query->where('status', '!=', 'paid');
                })
                ->count();

            // Moyenne générale de l'école
            $averageGrade = $school->students()
                ->join('grades', 'students.id', '=', 'grades.student_id')
                ->avg('grades.score') ?? 0;

            // Activités récentes
            $recentActivities = $this->getRecentActivities($school);

            return [
                'total_students' => $totalStudents,
                'male_students' => $maleStudents,
                'female_students' => $femaleStudents,
                'gender_percentage' => [
                    'male' => $totalStudents > 0 ? round(($maleStudents / $totalStudents) * 100, 1) : 0,
                    'female' => $totalStudents > 0 ? round(($femaleStudents / $totalStudents) * 100, 1) : 0
                ],
                'total_teachers' => $totalTeachers,
                'total_classrooms' => $totalClassrooms,
                'monthly_revenue' => $monthlyRevenue,
                'pending_payments' => $pendingPayments,
                'unpaid_students' => $unpaidStudents,
                'average_grade' => round($averageGrade, 2),
                'recent_activities' => $recentActivities
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Graphiques et données de visualisation
     */
    public function getCharts(School $school, Request $request)
    {
        try {
            $filters = $request->only(['teacher_id', 'classroom_id', 'academic_year_id']);
            
            // Évolution des programmes scolaires
            $programEvolution = $this->getProgramEvolution($school, $filters);
            
            // Performance par matière
            $subjectPerformance = $this->getSubjectPerformance($school, $filters);
            
            // Répartition des élèves par classe
            $classDistribution = $this->getClassDistribution($school);
            
            // Évolution des inscriptions
            $enrollmentTrend = $this->getEnrollmentTrend($school);

            // Évolution des paiements
            $paymentTrend = $this->getPaymentTrend($school);

            return [
                'program_evolution' => $programEvolution,
                'subject_performance' => $subjectPerformance,
                'class_distribution' => $classDistribution,
                'enrollment_trend' => $enrollmentTrend,
                'payment_trend' => $paymentTrend
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Évolution des programmes scolaires
     */
    private function getProgramEvolution(School $school, $filters = [])
    {
        $query = Grade::join('students', 'grades.student_id', '=', 'students.id')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->join('school_classes', 'students.class_id', '=', 'school_classes.id')
            ->where('students.school_id', $school->id)
            ->selectRaw('DATE_FORMAT(grades.created_at, "%Y-%m") as month, AVG(grades.score) as average_score')
            ->where('grades.created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month');

        if (isset($filters['teacher_id'])) {
            $query->where('grades.teacher_id', $filters['teacher_id']);
        }

        if (isset($filters['classroom_id'])) {
            $query->where('students.class_id', $filters['classroom_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('grades.academic_year_id', $filters['academic_year_id']);
        }

        return $query->get()->map(function ($item) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                'average_score' => round($item->average_score, 2)
            ];
        });
    }

    /**
     * Performance par matière
     */
    private function getSubjectPerformance(School $school, $filters = [])
    {
        $query = Subject::join('grades', 'subjects.id', '=', 'grades.subject_id')
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->where('students.school_id', $school->id)
            ->select('subjects.name', 'subjects.id')
            ->selectRaw('AVG(grades.score) as average_score, COUNT(grades.id) as total_grades')
            ->groupBy('subjects.id', 'subjects.name')
            ->orderByDesc('average_score');

        if (isset($filters['classroom_id'])) {
            $query->where('students.class_id', $filters['classroom_id']);
        }

        if (isset($filters['academic_year_id'])) {
            $query->where('grades.academic_year_id', $filters['academic_year_id']);
        }

        return $query->get()->map(function ($item) {
            return [
                'subject' => $item->name,
                'average_score' => round($item->average_score, 2),
                'total_grades' => $item->total_grades
            ];
        });
    }

    /**
     * Répartition des élèves par classe
     */
    private function getClassDistribution(School $school)
    {
        return SchoolClass::where('school_id', $school->id)
            ->withCount('students')
            ->get()
            ->map(function ($class) {
                return [
                    'class_name' => $class->name,
                    'students_count' => $class->students_count
                ];
            });
    }

    /**
     * Évolution des inscriptions
     */
    private function getEnrollmentTrend(School $school)
    {
        return Student::where('school_id', $school->id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'count' => $item->count
                ];
            });
    }

    /**
     * Évolution des paiements
     */
    private function getPaymentTrend(School $school)
    {
        return Payment::where('school_id', $school->id)
            ->where('status', 'completed')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'total' => $item->total,
                    'count' => $item->count
                ];
            });
    }

    /**
     * Activités récentes
     */
    private function getRecentActivities(School $school)
    {
        $activities = [];

        // Dernières inscriptions
        $recentStudents = Student::where('school_id', $school->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['name', 'created_at']);

        foreach ($recentStudents as $student) {
            $activities[] = [
                'type' => 'student_enrollment',
                'message' => "Nouvel élève inscrit: {$student->name}",
                'date' => $student->created_at,
                'icon' => 'user-plus',
                'color' => 'success'
            ];
        }

        // Derniers paiements
        $recentPayments = Payment::where('school_id', $school->id)
            ->where('status', 'completed')
            ->with('student:id,name')
            ->orderByDesc('created_at')
            ->take(5)
            ->get(['student_id', 'amount', 'created_at']);

        foreach ($recentPayments as $payment) {
            $activities[] = [
                'type' => 'payment',
                'message' => "Paiement reçu de {$payment->student->name}: {$payment->amount} FCFA",
                'date' => $payment->created_at,
                'icon' => 'credit-card',
                'color' => 'info'
            ];
        }

        // Trier par date décroissante
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Obtenir l'école courante
     */
    private function getCurrentSchool(Request $request)
    {
        // Dans un contexte multi-tenant, récupérer l'école depuis le tenant
        // Pour l'instant, on peut la récupérer depuis l'utilisateur connecté
        $user = $request->user();
        
        if ($user->isSuperAdmin()) {
            // Si c'est un super admin, il faut spécifier l'école
            $schoolId = $request->get('school_id');
            return $schoolId ? School::find($schoolId) : null;
        }
        
        // Pour les autres utilisateurs, récupérer la première école associée
        return $user->schools()->first();
    }

    /**
     * Filtres disponibles
     */
    public function getFilters(Request $request)
    {
        try {
            $school = $this->getCurrentSchool($request);
            
            if (!$school) {
                return response()->json([
                    'success' => false,
                    'message' => 'École non trouvée'
                ], 404);
            }

            $teachers = $school->users()
                ->where('global_role', 'teacher')
                ->select('id', 'name')
                ->get();

            $classrooms = $school->classes()
                ->select('id', 'name')
                ->get();

            $academicYears = $school->academicYears()
                ->select('id', 'name', 'start_date', 'end_date')
                ->orderByDesc('start_date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'teachers' => $teachers,
                    'classrooms' => $classrooms,
                    'academic_years' => $academicYears
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des filtres',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }
}
