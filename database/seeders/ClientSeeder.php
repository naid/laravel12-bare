<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $industries = ['Healthcare', 'Education', 'Finance', 'Technology', 'Retail', 'Manufacturing', 'Other'];
        $services_provided = ['Telehealth', 'In-Person', 'Phone', 'Video', 'Other'];

        //Seed 20 random sample clients
        for ($i = 0; $i < 20; $i++) {
            DB::table('clients')->insert([
                'name' => $faker->company,
                'industry' => $faker->randomElement($industries),
                'services_provided' => $faker->randomElement($services_provided),
                'ccn' => $faker->numberBetween(100000000000, 999999999999), // 12 digits
                'npi' => $faker->numberBetween(100000000000, 999999999999), // 12 digits
                'active' => $faker->randomElement(['1', '0']),
            ]);
        }
    }
}

