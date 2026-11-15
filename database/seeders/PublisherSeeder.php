<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Province;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::updateOrCreate([
            'name' => 'Argentina',
        ]);

        $province = Province::updateOrCreate(
            [
                'name' => 'Ciudad Autónoma de Buenos Aires',
                'country_id' => $country->id,
            ],
            []
        );

        $city = City::updateOrCreate(
            [
                'name' => 'Buenos Aires',
                'province_id' => $province->id,
            ],
            []
        );

        Publisher::updateOrCreate(
            ['name' => 'Editorial de prueba'],
            ['city_id' => $city->id]
        );
    }
}