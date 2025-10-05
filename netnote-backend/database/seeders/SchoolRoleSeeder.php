<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolRole;

class SchoolRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemRoles = [
            [
                'name' => 'Directeur',
                'slug' => 'director',
                'description' => 'Directeur de l\'école - Accès complet',
                'permissions' => ['*'], // Toutes les permissions
                'can_assign_roles' => true,
                'assignable_roles' => ['*'], // Peut assigner tous les rôles
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Directeur Adjoint',
                'slug' => 'deputy_director',
                'description' => 'Directeur adjoint - Gestion pédagogique et administrative',
                'permissions' => [
                    'manage_students',
                    'manage_teachers',
                    'manage_classes',
                    'manage_schedules',
                    'view_reports',
                    'manage_exams'
                ],
                'can_assign_roles' => true,
                'assignable_roles' => ['teacher', 'supervisor', 'secretary'],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Comptable',
                'slug' => 'accountant',
                'description' => 'Gestionnaire financier de l\'école',
                'permissions' => [
                    'manage_finances',
                    'view_payments',
                    'manage_fees',
                    'view_financial_reports',
                    'manage_transactions'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Surveillant',
                'slug' => 'supervisor',
                'description' => 'Surveillant général - Discipline et ordre',
                'permissions' => [
                    'manage_discipline',
                    'view_students',
                    'manage_attendance',
                    'view_schedules'
                ],
                'can_assign_roles' => true,
                'assignable_roles' => ['assistant_supervisor'],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Surveillant Adjoint',
                'slug' => 'assistant_supervisor',
                'description' => 'Assistant surveillant - Support discipline',
                'permissions' => [
                    'view_discipline',
                    'view_students',
                    'manage_attendance',
                    'view_schedules'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Secrétaire',
                'slug' => 'secretary',
                'description' => 'Secrétaire - Administration générale',
                'permissions' => [
                    'manage_administration',
                    'view_students',
                    'manage_communications',
                    'view_schedules'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Professeur',
                'slug' => 'teacher',
                'description' => 'Enseignant - Gestion pédagogique',
                'permissions' => [
                    'manage_own_classes',
                    'manage_grades',
                    'create_exams',
                    'view_own_students',
                    'manage_own_schedule'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Bibliothécaire',
                'slug' => 'librarian',
                'description' => 'Gestionnaire de la bibliothèque',
                'permissions' => [
                    'manage_library',
                    'view_students',
                    'manage_books'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Infirmier',
                'slug' => 'nurse',
                'description' => 'Personnel médical de l\'école',
                'permissions' => [
                    'manage_health',
                    'view_students',
                    'manage_medical_records'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ],
            [
                'name' => 'Technicien IT',
                'slug' => 'it_technician',
                'description' => 'Support technique informatique',
                'permissions' => [
                    'manage_technology',
                    'view_system_logs',
                    'manage_equipment'
                ],
                'can_assign_roles' => false,
                'assignable_roles' => [],
                'is_system' => true,
                'is_active' => true
            ]
        ];

        foreach ($systemRoles as $roleData) {
            SchoolRole::updateOrCreate(
                ['slug' => $roleData['slug'], 'is_system' => true],
                $roleData
            );
        }
    }
}