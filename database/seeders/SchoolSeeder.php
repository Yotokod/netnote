<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'École Primaire Sainte-Marie',
                'slug' => 'ecole-primaire-sainte-marie',
                'subdomain' => 'sainte-marie',
                'founder' => 'Sœur Marie-Claire',
                'year_founded' => 1985,
                'country_id' => 1, // Côte d'Ivoire
                'city_id' => 1, // Abidjan
                'quartier' => 'Cocody',
                'phones' => ['+225 22 44 55 66', '+225 07 08 09 10'],
                'email_pro' => 'contact@sainte-marie.edu.ci',
                'about' => 'École primaire catholique offrant un enseignement de qualité depuis plus de 35 ans.',
                'is_active' => true,
            ],
            [
                'name' => 'Collège Moderne de Yamoussoukro',
                'slug' => 'college-moderne-yamoussoukro',
                'subdomain' => 'college-yamoussoukro',
                'founder' => 'Dr. Kouassi Jean',
                'year_founded' => 1992,
                'country_id' => 1,
                'city_id' => 2, // Yamoussoukro
                'quartier' => 'Centre-ville',
                'phones' => ['+225 30 64 55 77'],
                'email_pro' => 'info@college-yamoussoukro.edu.ci',
                'about' => 'Établissement d\'enseignement secondaire moderne avec des infrastructures de pointe.',
                'is_active' => true,
            ],
            [
                'name' => 'Lycée Technique de Bouaké',
                'slug' => 'lycee-technique-bouake',
                'subdomain' => 'lycee-bouake',
                'founder' => 'Ministère de l\'Éducation Nationale',
                'year_founded' => 1978,
                'country_id' => 1,
                'city_id' => 3, // Bouaké
                'quartier' => 'Zone industrielle',
                'phones' => ['+225 31 63 44 55', '+225 05 06 07 08'],
                'email_pro' => 'direction@lycee-bouake.edu.ci',
                'about' => 'Lycée technique formant aux métiers industriels et technologiques.',
                'is_active' => true,
            ],
        ];

        foreach ($schools as $schoolData) {
            \App\Models\School::create($schoolData);
        }
    }
}
