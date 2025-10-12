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

        $industries = ['Healthcare', 'Technology', 'Finance', 'Education', 'Manufacturing', 'Retail', 'Real Estate', 'Energy', 'Logistics', 'Hospitality'];
        $services = [
            'Healthcare' => 'Medical Billing, Insurance Claims, Patient Care',
            'Technology' => 'Software Development, IT Consulting, Cloud Services',
            'Finance' => 'Financial Planning, Investment Management, Accounting',
            'Education' => 'Online Learning, Training Programs, Certification',
            'Manufacturing' => 'Production, Quality Control, Supply Chain',
            'Retail' => 'E-commerce, Inventory Management, Customer Service',
            'Real Estate' => 'Property Management, Sales, Leasing',
            'Energy' => 'Solar Installation, Energy Consulting, Utilities',
            'Logistics' => 'Freight, Warehousing, Distribution',
            'Hospitality' => 'Hotel Management, Event Planning, Catering',
        ];

        for ($i = 0; $i < 20; $i++) {
            $industry = $faker->randomElement($industries);
            $isHealthcare = in_array($industry, ['Healthcare']);

            DB::table('clients')->insert([
                'name' => $faker->company(),
                'industry' => $industry,
                'services_provided' => $services[$industry],
                'ccn' => $isHealthcare ? $faker->numerify('############') : null,
                'npi' => $isHealthcare ? $faker->numerify('##########') : null,
                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'state' => $faker->state(),
                'state_code' => $faker->stateAbbr(),
                'postal_code' => $faker->postcode(),
                'country' => 'USA',
                'contact_email' => $faker->companyEmail(),
                'contact_phone' => $faker->phoneNumber(),
                'contact_number' => $faker->phoneNumber(),
                'website_url' => $faker->url(),
                'created_on' => now(),
                'created_by' => 1,
                'updated_on' => now(),
                'updated_by' => 1,
                'active' => $faker->randomElement(['1', '1', '1', '0']), // 75% active
            ]);
        }
    }
}
