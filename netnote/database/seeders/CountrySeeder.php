<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Bénin', 'code' => 'BJ'],
            ['name' => 'Burkina Faso', 'code' => 'BF'],
            ['name' => 'Côte d\'Ivoire', 'code' => 'CI'],
            ['name' => 'Ghana', 'code' => 'GH'],
            ['name' => 'Mali', 'code' => 'ML'],
            ['name' => 'Niger', 'code' => 'NE'],
            ['name' => 'Nigeria', 'code' => 'NG'],
            ['name' => 'Sénégal', 'code' => 'SN'],
            ['name' => 'Togo', 'code' => 'TG'],
            ['name' => 'Cameroun', 'code' => 'CM'],
            ['name' => 'Gabon', 'code' => 'GA'],
            ['name' => 'République Démocratique du Congo', 'code' => 'CD'],
            ['name' => 'Kenya', 'code' => 'KE'],
            ['name' => 'Afrique du Sud', 'code' => 'ZA'],
            ['name' => 'Maroc', 'code' => 'MA'],
            ['name' => 'Tunisie', 'code' => 'TN'],
            ['name' => 'Algérie', 'code' => 'DZ'],
            ['name' => 'Égypte', 'code' => 'EG'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}