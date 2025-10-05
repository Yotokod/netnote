<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard principal Super Admin
     */
    public function index(Request $request)
    {
        try {
            $stats = $this->getStats();
            $charts = $this->getCharts($request);
            
            return response()->json([
                'success' => true,
                'data' => [
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
     * Statistiques globales
     */
    public function getStats()
    {
        try {
            $totalSchools = School::count();
            $activeSchools = School::where('is_active', true)->count();
            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $totalPayments = Payment::where('status', 'completed')->sum('amount');
            $monthlyRevenue = Payment::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');

            // Top 5 écoles les plus actives
            $topSchools = School::withCount(['students', 'users', 'payments'])
                ->orderByDesc('students_count')
                ->take(5)
                ->get();

            // Alertes système
            $systemAlerts = $this->getSystemAlerts();

            return [
                'total_schools' => $totalSchools,
                'active_schools' => $activeSchools,
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_payments' => $totalPayments,
                'monthly_revenue' => $monthlyRevenue,
                'top_schools' => $topSchools,
                'system_alerts' => $systemAlerts
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Graphiques et données de visualisation
     */
    public function getCharts(Request $request)
    {
        try {
            $filters = $request->only(['school_id', 'class_id', 'city_id', 'gender']);
            
            // Courbe d'évolution des inscriptions (12 derniers mois)
            $enrollmentEvolution = $this->getEnrollmentEvolution($filters);
            
            // Répartition par genre
            $genderDistribution = $this->getGenderDistribution($filters);
            
            // Performances par école
            $schoolPerformances = $this->getSchoolPerformances($filters);
            
            // Évolution des revenus
            $revenueEvolution = $this->getRevenueEvolution($filters);

            return [
                'enrollment_evolution' => $enrollmentEvolution,
                'gender_distribution' => $genderDistribution,
                'school_performances' => $schoolPerformances,
                'revenue_evolution' => $revenueEvolution
            ];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Évolution des inscriptions
     */
    private function getEnrollmentEvolution($filters = [])
    {
        $query = Student::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month');

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        return $query->get()->map(function ($item) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                'count' => $item->count
            ];
        });
    }

    /**
     * Répartition par genre
     */
    private function getGenderDistribution($filters = [])
    {
        $query = Student::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender');

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->get()->map(function ($item) {
            return [
                'gender' => $item->gender,
                'count' => $item->count,
                'percentage' => round(($item->count / Student::count()) * 100, 2)
            ];
        });
    }

    /**
     * Performances des écoles
     */
    private function getSchoolPerformances($filters = [])
    {
        $query = School::with(['students', 'payments'])
            ->withCount(['students', 'users as teachers_count' => function ($q) {
                $q->where('global_role', 'teacher');
            }])
            ->withSum('payments as total_revenue', 'amount');

        if (isset($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        return $query->take(10)->get()->map(function ($school) {
            $averageGrade = $school->students()
                ->join('grades', 'students.id', '=', 'grades.student_id')
                ->avg('grades.score') ?? 0;

            return [
                'school_name' => $school->name,
                'students_count' => $school->students_count,
                'teachers_count' => $school->teachers_count,
                'total_revenue' => $school->total_revenue ?? 0,
                'average_grade' => round($averageGrade, 2),
                'progress' => min(100, ($school->students_count / 1000) * 100) // Exemple de calcul de progression
            ];
        });
    }

    /**
     * Évolution des revenus
     */
    private function getRevenueEvolution($filters = [])
    {
        $query = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month');

        if (isset($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        return $query->get()->map(function ($item) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                'total' => $item->total
            ];
        });
    }

    /**
     * Alertes système
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // Écoles inactives depuis plus de 30 jours
        $inactiveSchools = School::where('is_active', false)
            ->where('updated_at', '<', Carbon::now()->subDays(30))
            ->count();

        if ($inactiveSchools > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "$inactiveSchools école(s) inactive(s) depuis plus de 30 jours",
                'action' => 'Vérifier les écoles inactives'
            ];
        }

        // Utilisateurs sans connexion depuis 30 jours
        $inactiveUsers = User::where('last_login_at', '<', Carbon::now()->subDays(30))
            ->orWhereNull('last_login_at')
            ->count();

        if ($inactiveUsers > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "$inactiveUsers utilisateur(s) sans connexion récente",
                'action' => 'Relancer les utilisateurs inactifs'
            ];
        }

        // Paiements en échec
        $failedPayments = Payment::where('status', 'failed')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        if ($failedPayments > 0) {
            $alerts[] = [
                'type' => 'error',
                'message' => "$failedPayments paiement(s) en échec cette semaine",
                'action' => 'Vérifier les paiements échoués'
            ];
        }

        return $alerts;
    }

    /**
     * Notifications récentes
     */
    public function getRecentNotifications(Request $request)
    {
        try {
            $notifications = DB::table('notification_logs')
                ->join('schools', 'notification_logs.school_id', '=', 'schools.id')
                ->select('notification_logs.*', 'schools.name as school_name')
                ->orderByDesc('notification_logs.created_at')
                ->take(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $notifications
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Filtres disponibles pour les graphiques
     */
    public function getFilters()
    {
        try {
            $schools = School::select('id', 'name')->where('is_active', true)->get();
            $cities = DB::table('cities')->select('id', 'name')->get();
            $classes = DB::table('school_classes')->select('id', 'name')->distinct()->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'schools' => $schools,
                    'cities' => $cities,
                    'classes' => $classes,
                    'genders' => ['M', 'F']
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
