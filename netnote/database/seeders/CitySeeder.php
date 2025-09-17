<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $citiesByCountry = [
            'Bénin' => ['Cotonou', 'Porto-Novo', 'Parakou', 'Abomey-Calavi', 'Djougou'],
            'Burkina Faso' => ['Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Ouahigouya', 'Banfora'],
            'Côte d\'Ivoire' => ['Abidjan', 'Yamoussoukro', 'Bouaké', 'Daloa', 'San-Pédro'],
            'Ghana' => ['Accra', 'Kumasi', 'Tamale', 'Takoradi', 'Cape Coast'],
            'Mali' => ['Bamako', 'Sikasso', 'Mopti', 'Koutiala', 'Kayes'],
            'Niger' => ['Niamey', 'Zinder', 'Maradi', 'Agadez', 'Tahoua'],
            'Nigeria' => ['Lagos', 'Kano', 'Ibadan', 'Abuja', 'Port Harcourt'],
            'Sénégal' => ['Dakar', 'Thiès', 'Kaolack', 'Saint-Louis', 'Ziguinchor'],
            'Togo' => ['Lomé', 'Sokodé', 'Kara', 'Palimé', 'Atakpamé'],
            'Cameroun' => ['Yaoundé', 'Douala', 'Garoua', 'Bamenda', 'Maroua'],
            'Gabon' => ['Libreville', 'Port-Gentil', 'Franceville', 'Oyem', 'Moanda'],
            'République Démocratique du Congo' => ['Kinshasa', 'Lubumbashi', 'Mbuji-Mayi', 'Kisangani', 'Kananga'],
            'Kenya' => ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret'],
            'Afrique du Sud' => ['Johannesburg', 'Cape Town', 'Durban', 'Pretoria', 'Port Elizabeth'],
            'Maroc' => ['Casablanca', 'Rabat', 'Fès', 'Marrakech', 'Agadir'],
            'Tunisie' => ['Tunis', 'Sfax', 'Sousse', 'Kairouan', 'Bizerte'],
            'Algérie' => ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida'],
            'Égypte' => ['Le Caire', 'Alexandrie', 'Gizeh', 'Charm el-Cheikh', 'Louxor'],
        ];

        foreach ($citiesByCountry as $countryName => $cities) {
            $country = Country::where('name', $countryName)->first();
            
            if ($country) {
                foreach ($cities as $cityName) {
                    City::create([
                        'name' => $cityName,
                        'country_id' => $country->id,
                    ]);
                }
            }
        }
    }
}