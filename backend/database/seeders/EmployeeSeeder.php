<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_MX');
        
        $stores = DB::table('stores')->get();
        
        if ($stores->isEmpty()) {
            $this->command->warn('No hay tiendas en la base de datos. Por favor, crea tiendas primero.');
            return;
        }

        $positions = ['Manager', 'Supervisor', 'Cashier', 'Stock', 'Sales', 'Other'];
        $statuses = ['Active', 'Inactive', 'On Leave'];

        $employees = [];

        for ($i = 0; $i < 20; $i++) {
            $status = $faker->randomElement($statuses);
            $hireDate = $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d');
            $endDate = $status === 'Inactive' ? $faker->dateTimeBetween($hireDate, 'now')->format('Y-m-d') : null;

            $employees[] = [
                'full_name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'document_type' => 'DNI',
                'document_number' => $faker->unique()->numerify('########[A-Z]'),
                'position' => $faker->randomElement($positions),
                'salary' => $faker->randomFloat(2, 5000, 30000),
                'status' => $status,
                'hire_date' => $hireDate,
                'end_date' => $endDate,
                'fk_id_user' => null,
                'fk_id_store' => $stores->random()->id_store,
                'notes' => $faker->optional()->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('employees')->insert($employees);

        $this->command->info('✅ ' . count($employees) . ' empleados creados exitosamente.');
    }
}
