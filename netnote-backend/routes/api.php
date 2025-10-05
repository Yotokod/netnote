<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Api\SuperAdmin\SchoolController as SuperAdminSchoolController;
use App\Http\Controllers\Api\SuperAdmin\ParameterController;
use App\Http\Controllers\Api\SuperAdmin\TemplateController;
use App\Http\Controllers\Api\SuperAdmin\LanguageController;
use App\Http\Controllers\Api\SuperAdmin\ThemeController;
use App\Http\Controllers\Api\SuperAdmin\PlanController;
use App\Http\Controllers\Api\SuperAdmin\BillingController;
use App\Http\Controllers\Api\SuperAdmin\LogController;
use App\Http\Controllers\Api\SuperAdmin\NotificationController;
use App\Http\Controllers\Api\SuperAdmin\IntegrationController;
use App\Http\Controllers\Api\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Api\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\Api\School\AboutController;
use App\Http\Controllers\Api\School\AcademicYearController;
use App\Http\Controllers\Api\School\StudentController;
use App\Http\Controllers\Api\School\TeacherController;
use App\Http\Controllers\Api\School\StaffController;
use App\Http\Controllers\Api\School\ClassroomController;
use App\Http\Controllers\Api\School\ExamController;
use App\Http\Controllers\Api\School\ScheduleController;
use App\Http\Controllers\Api\School\TuitionController;
use App\Http\Controllers\Api\School\TransactionController;
use App\Http\Controllers\Api\School\NotificationController as SchoolNotificationController;
use App\Http\Controllers\Api\School\DisciplinaryController;
use App\Http\Controllers\Api\School\SettingsController;
use App\Http\Controllers\Api\Finance\DashboardController as FinanceDashboardController;
use App\Http\Controllers\Api\Finance\TuitionController as FinanceTuitionController;
use App\Http\Controllers\Api\Finance\TransactionController as FinanceTransactionController;
use App\Http\Controllers\Api\Finance\FinancialManagementController;
use App\Http\Controllers\Api\Finance\ReportController;
use App\Http\Controllers\Api\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Api\Teacher\GradeController;
use App\Http\Controllers\Api\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Api\Teacher\BulletinController;
use App\Http\Controllers\Api\Teacher\ClassController;
use App\Http\Controllers\Api\Teacher\SubjectController;
use App\Http\Controllers\Api\Teacher\TodoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes d'authentification
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('verify-2fa', [AuthController::class, 'verify2FA']);
    Route::post('enable-2fa', [AuthController::class, 'enable2FA'])->middleware('auth:sanctum');
    Route::post('disable-2fa', [AuthController::class, 'disable2FA'])->middleware('auth:sanctum');
});

// Routes Super Admin
Route::prefix('super-admin')->middleware(['auth:sanctum', 'role:super_admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [SuperAdminDashboardController::class, 'index']);
    Route::get('dashboard/stats', [SuperAdminDashboardController::class, 'getStats']);
    Route::get('dashboard/charts', [SuperAdminDashboardController::class, 'getCharts']);
    Route::get('dashboard/filters', [SuperAdminDashboardController::class, 'getFilters']);
    
    // Gestion des écoles
    Route::apiResource('schools', SuperAdminSchoolController::class);
    Route::post('schools/{school}/activate', [SuperAdminSchoolController::class, 'activate']);
    Route::post('schools/{school}/deactivate', [SuperAdminSchoolController::class, 'deactivate']);
    Route::get('schools/{school}/stats', [SuperAdminSchoolController::class, 'getSchoolStats']);
    
    // Paramètres
    Route::prefix('parameters')->group(function () {
        Route::get('/', [ParameterController::class, 'index']);
        Route::post('countries', [ParameterController::class, 'storeCountry']);
        Route::put('countries/{country}', [ParameterController::class, 'updateCountry']);
        Route::delete('countries/{country}', [ParameterController::class, 'deleteCountry']);
        Route::post('cities', [ParameterController::class, 'storeCity']);
        Route::put('cities/{city}', [ParameterController::class, 'updateCity']);
        Route::delete('cities/{city}', [ParameterController::class, 'deleteCity']);
        Route::post('religions', [ParameterController::class, 'storeReligion']);
        Route::put('religions/{religion}', [ParameterController::class, 'updateReligion']);
        Route::delete('religions/{religion}', [ParameterController::class, 'deleteReligion']);
        Route::post('genders', [ParameterController::class, 'storeGender']);
        Route::put('genders/{gender}', [ParameterController::class, 'updateGender']);
        Route::delete('genders/{gender}', [ParameterController::class, 'deleteGender']);
        Route::post('roles', [ParameterController::class, 'storeRole']);
        Route::put('roles/{role}', [ParameterController::class, 'updateRole']);
        Route::delete('roles/{role}', [ParameterController::class, 'deleteRole']);
        Route::post('class-types', [ParameterController::class, 'storeClassType']);
        Route::put('class-types/{classType}', [ParameterController::class, 'updateClassType']);
        Route::delete('class-types/{classType}', [ParameterController::class, 'deleteClassType']);
        Route::post('subject-types', [ParameterController::class, 'storeSubjectType']);
        Route::put('subject-types/{subjectType}', [ParameterController::class, 'updateSubjectType']);
        Route::delete('subject-types/{subjectType}', [ParameterController::class, 'deleteSubjectType']);
    });
    
    // Templates
    Route::apiResource('templates', TemplateController::class);
    Route::get('templates/{template}/preview', [TemplateController::class, 'preview']);
    Route::post('templates/{template}/duplicate', [TemplateController::class, 'duplicate']);
    
    // Langues
    Route::prefix('languages')->group(function () {
        Route::get('/', [LanguageController::class, 'index']);
        Route::post('/', [LanguageController::class, 'store']);
        Route::put('{language}', [LanguageController::class, 'update']);
        Route::delete('{language}', [LanguageController::class, 'destroy']);
        Route::get('{language}/translations', [LanguageController::class, 'getTranslations']);
        Route::put('{language}/translations', [LanguageController::class, 'updateTranslations']);
        Route::post('set-default', [LanguageController::class, 'setDefault']);
    });
    
    // Thème
    Route::prefix('theme')->group(function () {
        Route::get('/', [ThemeController::class, 'index']);
        Route::put('/', [ThemeController::class, 'update']);
        Route::post('reset', [ThemeController::class, 'reset']);
    });
    
    // Plans et tarifs
    Route::apiResource('plans', PlanController::class);
    Route::post('plans/{plan}/activate', [PlanController::class, 'activate']);
    Route::post('plans/{plan}/deactivate', [PlanController::class, 'deactivate']);
    
    // Facturation
    Route::prefix('billing')->group(function () {
        Route::get('history', [BillingController::class, 'history']);
        Route::get('invoices', [BillingController::class, 'invoices']);
        Route::get('invoices/{invoice}', [BillingController::class, 'showInvoice']);
        Route::post('invoices/{invoice}/send', [BillingController::class, 'sendInvoice']);
    });
    
    // Logs et audits
    Route::prefix('logs')->group(function () {
        Route::get('/', [LogController::class, 'index']);
        Route::get('activities', [LogController::class, 'activities']);
        Route::get('system', [LogController::class, 'systemLogs']);
        Route::delete('clear', [LogController::class, 'clearLogs']);
    });
    
    // Notifications globales
    Route::apiResource('notifications', NotificationController::class);
    Route::post('notifications/{notification}/send', [NotificationController::class, 'send']);
    Route::post('notifications/broadcast', [NotificationController::class, 'broadcast']);
    
    // Intégrations
    Route::prefix('integrations')->group(function () {
        Route::get('/', [IntegrationController::class, 'index']);
        Route::post('ai', [IntegrationController::class, 'configureAI']);
        Route::post('payment', [IntegrationController::class, 'configurePayment']);
        Route::post('sms', [IntegrationController::class, 'configureSMS']);
        Route::post('test/{integration}', [IntegrationController::class, 'testIntegration']);
    });
    
    // Gestion des utilisateurs
    Route::apiResource('users', SuperAdminUserController::class);
    Route::post('users/{user}/activate', [SuperAdminUserController::class, 'activate']);
    Route::post('users/{user}/deactivate', [SuperAdminUserController::class, 'deactivate']);
    Route::get('users/{user}/stats', [SuperAdminUserController::class, 'getUserStats']);
    Route::get('users/{user}/activities', [SuperAdminUserController::class, 'getUserActivities']);
});

// Routes École (Admin École)
Route::prefix('school')->middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Dashboard
    Route::get('dashboard', [SchoolDashboardController::class, 'index']);
    Route::get('dashboard/stats', [SchoolDashboardController::class, 'getStats']);
    Route::get('dashboard/charts', [SchoolDashboardController::class, 'getCharts']);
    
    // À propos
    Route::prefix('about')->group(function () {
        Route::get('/', [AboutController::class, 'index']);
        Route::put('/', [AboutController::class, 'update']);
        Route::post('images', [AboutController::class, 'uploadImage']);
        Route::delete('images/{image}', [AboutController::class, 'deleteImage']);
        Route::post('logo', [AboutController::class, 'uploadLogo']);
    });
    
    // Années scolaires
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate']);
    
    // Élèves
    Route::apiResource('students', StudentController::class);
    Route::get('students/{student}/bulletin', [StudentController::class, 'getBulletin']);
    Route::get('students/{student}/payments', [StudentController::class, 'getPayments']);
    Route::post('students/{student}/assign-classroom', [StudentController::class, 'assignClassroom']);
    Route::post('students/bulk-import', [StudentController::class, 'bulkImport']);
    Route::get('students/export', [StudentController::class, 'export']);
    
    // Professeurs
    Route::apiResource('teachers', TeacherController::class);
    Route::post('teachers/{teacher}/assign-subjects', [TeacherController::class, 'assignSubjects']);
    Route::get('teachers/{teacher}/schedule', [TeacherController::class, 'getSchedule']);
    Route::get('teachers/{teacher}/classes', [TeacherController::class, 'getClasses']);
    
    // Staff
    Route::apiResource('staff', StaffController::class);
    Route::post('staff/{staff}/assign-role', [StaffController::class, 'assignRole']);
    
    // Salles de cours
    Route::apiResource('classrooms', ClassroomController::class);
    Route::get('classrooms/{classroom}/students', [ClassroomController::class, 'getStudents']);
    Route::get('classrooms/{classroom}/schedule', [ClassroomController::class, 'getSchedule']);
    
    // Examens et notes
    Route::apiResource('exams', ExamController::class);
    Route::post('exams/{exam}/grades', [ExamController::class, 'storeGrades']);
    Route::get('exams/{exam}/grades', [ExamController::class, 'getGrades']);
    Route::post('exams/{exam}/send-results', [ExamController::class, 'sendResults']);
    Route::get('exams/{exam}/export-sheet', [ExamController::class, 'exportGradeSheet']);
    
    // Emploi du temps
    Route::apiResource('schedules', ScheduleController::class);
    Route::get('schedules/calendar', [ScheduleController::class, 'getCalendar']);
    Route::post('schedules/bulk-create', [ScheduleController::class, 'bulkCreate']);
    
    // Scolarité
    Route::prefix('tuition')->group(function () {
        Route::get('/', [TuitionController::class, 'index']);
        Route::get('stats', [TuitionController::class, 'getStats']);
        Route::get('unpaid-students', [TuitionController::class, 'getUnpaidStudents']);
        Route::post('payment', [TuitionController::class, 'recordPayment']);
        Route::get('movements', [TuitionController::class, 'getMovements']);
    });
    
    // Transactions
    Route::apiResource('transactions', TransactionController::class);
    Route::get('transactions/export', [TransactionController::class, 'export']);
    
    // Notifications
    Route::apiResource('notifications', SchoolNotificationController::class);
    Route::post('notifications/{notification}/mark-read', [SchoolNotificationController::class, 'markAsRead']);
    
    // Punitions et récompenses
    Route::apiResource('disciplinary-actions', DisciplinaryController::class);
    Route::get('disciplinary-actions/{student}/history', [DisciplinaryController::class, 'getStudentHistory']);
    
    // Paramètres école
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index']);
        Route::put('/', [SettingsController::class, 'update']);
        Route::post('backup', [SettingsController::class, 'createBackup']);
        Route::get('backups', [SettingsController::class, 'getBackups']);
    });
});

// Routes Financier
Route::prefix('finance')->middleware(['auth:sanctum', 'tenant', 'role:financier'])->group(function () {
    // Dashboard
    Route::get('dashboard', [FinanceDashboardController::class, 'index']);
    Route::get('dashboard/stats', [FinanceDashboardController::class, 'getStats']);
    
    // Scolarité
    Route::prefix('tuition')->group(function () {
        Route::get('/', [FinanceTuitionController::class, 'index']);
        Route::post('payment', [FinanceTuitionController::class, 'recordPayment']);
        Route::get('reports', [FinanceTuitionController::class, 'getReports']);
    });
    
    // Transactions
    Route::apiResource('transactions', FinanceTransactionController::class);
    
    // Gestion financière
    Route::prefix('management')->group(function () {
        Route::post('deposit', [FinancialManagementController::class, 'deposit']);
        Route::post('withdrawal', [FinancialManagementController::class, 'withdrawal']);
        Route::get('balance', [FinancialManagementController::class, 'getBalance']);
        Route::get('movements', [FinancialManagementController::class, 'getMovements']);
    });
    
    // Rapports comptables
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('income', [ReportController::class, 'incomeReport']);
        Route::get('expenses', [ReportController::class, 'expensesReport']);
        Route::get('balance-sheet', [ReportController::class, 'balanceSheet']);
        Route::get('cash-flow', [ReportController::class, 'cashFlow']);
        Route::post('generate', [ReportController::class, 'generateReport']);
    });
});

// Routes Professeur
Route::prefix('teacher')->middleware(['auth:sanctum', 'tenant', 'role:teacher'])->group(function () {
    // Dashboard
    Route::get('dashboard', [TeacherDashboardController::class, 'index']);
    Route::get('dashboard/stats', [TeacherDashboardController::class, 'getStats']);
    
    // Notes et évaluations
    Route::apiResource('grades', GradeController::class);
    Route::post('grades/bulk-create', [GradeController::class, 'bulkCreate']);
    Route::get('grades/student/{student}', [GradeController::class, 'getStudentGrades']);
    
    // Examens et devoirs
    Route::apiResource('exams', TeacherExamController::class);
    Route::post('exams/generate-ai', [TeacherExamController::class, 'generateWithAI']);
    Route::get('exams/{exam}/results', [TeacherExamController::class, 'getResults']);
    
    // Bulletins
    Route::prefix('bulletins')->group(function () {
        Route::get('/', [BulletinController::class, 'index']);
        Route::get('student/{student}', [BulletinController::class, 'getStudentBulletin']);
        Route::post('generate', [BulletinController::class, 'generate']);
        Route::get('{bulletin}/download', [BulletinController::class, 'download']);
    });
    
    // Classes
    Route::get('classes', [ClassController::class, 'index']);
    Route::get('classes/{class}/students', [ClassController::class, 'getStudents']);
    
    // Matières
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::get('subjects/{subject}/students', [SubjectController::class, 'getStudents']);
    
    // To-do
    Route::apiResource('todos', TodoController::class);
    Route::post('todos/{todo}/complete', [TodoController::class, 'markAsComplete']);
});

// Routes communes (profil utilisateur)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/', function (Request $request) {
            return $request->user();
        });
        Route::put('/', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('upload-avatar', [AuthController::class, 'uploadAvatar']);
    });
});

// Routes publiques (landing page, inscription école)
Route::prefix('public')->group(function () {
    Route::post('school-registration', [AuthController::class, 'schoolRegistration']);
    Route::get('plans', [PlanController::class, 'publicIndex']);
    Route::get('templates/landing', [TemplateController::class, 'publicLandingTemplates']);
    Route::get('templates/bulletin', [TemplateController::class, 'publicBulletinTemplates']);
    Route::post('contact', [AuthController::class, 'contact']);
});
