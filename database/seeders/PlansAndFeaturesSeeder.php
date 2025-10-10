<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansAndFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les fonctionnalités
        $features = [
            [
                'name' => 'Gestion des élèves',
                'slug' => 'student-management',
                'description' => 'Inscriptions, dossiers complets, suivi des parents',
                'icon' => 'fas fa-users',
                'sort_order' => 1,
            ],
            [
                'name' => 'Gestion des notes',
                'slug' => 'grade-management',
                'description' => 'Saisie des notes, bulletins, statistiques',
                'icon' => 'fas fa-chart-line',
                'sort_order' => 2,
            ],
            [
                'name' => 'Emploi du temps',
                'slug' => 'schedule-management',
                'description' => 'Planification automatique, gestion des salles',
                'icon' => 'fas fa-calendar-alt',
                'sort_order' => 3,
            ],
            [
                'name' => 'Gestion financière',
                'slug' => 'financial-management',
                'description' => 'Frais de scolarité, paiements, factures',
                'icon' => 'fas fa-money-bill-wave',
                'sort_order' => 4,
            ],
            [
                'name' => 'Communication',
                'slug' => 'communication',
                'description' => 'SMS, emails, notifications push',
                'icon' => 'fas fa-comments',
                'sort_order' => 5,
            ],
            [
                'name' => 'Rapports avancés',
                'slug' => 'advanced-reports',
                'description' => 'Tableaux de bord, statistiques avancées',
                'icon' => 'fas fa-chart-bar',
                'sort_order' => 6,
            ],
            [
                'name' => 'Domaine personnalisé',
                'slug' => 'custom-domain',
                'description' => 'Votre propre nom de domaine',
                'icon' => 'fas fa-globe',
                'sort_order' => 7,
            ],
            [
                'name' => 'Support prioritaire',
                'slug' => 'priority-support',
                'description' => 'Support technique prioritaire 24/7',
                'icon' => 'fas fa-headset',
                'sort_order' => 8,
            ],
        ];

        foreach ($features as $feature) {
            \App\Models\Feature::create($feature);
        }

        // Créer les plans
        $plans = [
            [
                'name' => 'Starter',
                'description' => 'Parfait pour les petites écoles',
                'price' => 25000,
                'billing_cycle' => 'monthly',
                'max_students' => 100,
                'max_teachers' => 10,
                'max_storage_gb' => 5,
                'has_custom_domain' => false,
                'has_advanced_reports' => false,
                'has_api_access' => false,
                'has_priority_support' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'description' => 'Idéal pour les établissements moyens',
                'price' => 50000,
                'billing_cycle' => 'monthly',
                'max_students' => 500,
                'max_teachers' => 50,
                'max_storage_gb' => 20,
                'has_custom_domain' => true,
                'has_advanced_reports' => true,
                'has_api_access' => false,
                'has_priority_support' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Pour les grandes institutions',
                'price' => 100000,
                'billing_cycle' => 'monthly',
                'max_students' => null, // illimité
                'max_teachers' => null, // illimité
                'max_storage_gb' => null, // illimité
                'has_custom_domain' => true,
                'has_advanced_reports' => true,
                'has_api_access' => true,
                'has_priority_support' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            $plan = \App\Models\Plan::create($planData);
            
            // Associer les fonctionnalités aux plans
            $featureIds = \App\Models\Feature::pluck('id')->toArray();
            
            if ($plan->name === 'Starter') {
                // Plan Starter : fonctionnalités de base
                $plan->features()->attach([1, 2, 3, 4, 5]); // Exclut rapports avancés, domaine personnalisé, support prioritaire
            } elseif ($plan->name === 'Professional') {
                // Plan Professional : toutes sauf API et support prioritaire
                $plan->features()->attach([1, 2, 3, 4, 5, 6, 7]);
            } else {
                // Plan Enterprise : toutes les fonctionnalités
                $plan->features()->attach($featureIds);
            }
        }
    }
}
