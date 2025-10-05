<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le pays (Cameroun) et une ville
        $cameroon = \App\Models\Country::where('name', 'Cameroun')->first();
        $city = \App\Models\City::where('country_id', $cameroon->id)->first();

        // Créer une école de démonstration
        $demoSchool = School::firstOrCreate(
            ['subdomain' => 'demo-ecole'],
            [
                'name' => 'École de Démonstration',
                'slug' => 'ecole-demonstration',
                'subdomain' => 'demo-ecole',
                'founder' => 'NetNote Team',
                'year_founded' => 2024,
                'country_id' => $cameroon->id,
                'city_id' => $city->id,
                'quartier' => 'Centre-ville',
                'email_pro' => 'contact@demo-ecole.netnote.cm',
                'phones' => ['+237 6XX XXX XXX'],
                'is_active' => true,
            ]
        );

        // 1. Administrateur d'école
        $schoolAdmin = User::firstOrCreate(
            ['email' => 'admin@demo-ecole.netnote.cm'],
            [
                'name' => 'Admin Démo',
                'email' => 'admin@demo-ecole.netnote.cm',
                'password' => Hash::make('DemoAdmin2024'),
                'global_role' => 'school_admin',
                'is_active' => true,
            ]
        );

        // Attacher l'admin à l'école
        if (!$schoolAdmin->schools()->where('school_id', $demoSchool->id)->exists()) {
            $schoolAdmin->schools()->attach($demoSchool->id, [
                'role_at_school' => 'admin',
                'is_active' => true,
            ]);
        }

        // 2. Responsable financier
        $financier = User::firstOrCreate(
            ['email' => 'finance@demo-ecole.netnote.cm'],
            [
                'name' => 'Financier Démo',
                'email' => 'finance@demo-ecole.netnote.cm',
                'password' => Hash::make('DemoFinance2024'),
                'global_role' => 'financier',
                'is_active' => true,
            ]
        );

        // Attacher le financier à l'école
        if (!$financier->schools()->where('school_id', $demoSchool->id)->exists()) {
            $financier->schools()->attach($demoSchool->id, [
                'role_at_school' => 'financier',
                'is_active' => true,
            ]);
        }

        // 3. Professeur
        $teacher = User::firstOrCreate(
            ['email' => 'prof@demo-ecole.netnote.cm'],
            [
                'name' => 'Professeur Démo',
                'email' => 'prof@demo-ecole.netnote.cm',
                'password' => Hash::make('DemoProf2024'),
                'global_role' => 'teacher',
                'is_active' => true,
            ]
        );

        // Attacher le professeur à l'école
        if (!$teacher->schools()->where('school_id', $demoSchool->id)->exists()) {
            $teacher->schools()->attach($demoSchool->id, [
                'role_at_school' => 'teacher',
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Comptes de démonstration créés avec succès !');
        $this->command->info('');
        $this->command->info('📧 Administrateur École: admin@demo-ecole.netnote.cm / DemoAdmin2024');
        $this->command->info('💰 Financier: finance@demo-ecole.netnote.cm / DemoFinance2024');
        $this->command->info('👨‍🏫 Professeur: prof@demo-ecole.netnote.cm / DemoProf2024');
    }
}

