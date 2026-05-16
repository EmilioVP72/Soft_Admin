<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->orderBy('id_store')->get();
        $suppliers = DB::table('suppliers')->get();
        $faker = Faker::create('es_MX');
        
        if ($stores->isEmpty() || $suppliers->isEmpty()) {
            $this->command->warn('Stores or Suppliers missing. Skipping DepartmentSeeder.');
            return;
        }

        $possibleDeps = [
            'Abarrotes' => ['Abarrotes Básicos', 'Enlatados', 'Semillas y Cereales'],
            'Lácteos' => ['Lácteos', 'Quesos', 'Yogures'],
            'Carnes' => ['Carnes Rojas', 'Aves', 'Pescados'],
            'Frutas y Verduras' => ['Frutas Frescas', 'Verduras', 'Legumbres'],
            'Farmacia' => ['Medicamentos', 'Cuidado Personal', 'Suplementos'],
            'Electrónica' => ['Audio y Video', 'Computación', 'Telefonía'],
            'Hogar' => ['Limpieza', 'Blancos', 'Decoración'],
            'Ferretería' => ['Herramientas', 'Eléctrico', 'Plomería']
        ];

        foreach ($stores as $store) {
            // Pick 3 to 5 random general departments for this store
            $numDeps = rand(3, 5);
            $selectedDeps = $faker->randomElements(array_keys($possibleDeps), $numDeps);

            foreach ($selectedDeps as $gDepName) {
                // 1. Crear la generalización (general_deps) para la tienda
                $gDepartmentId = DB::table('general_deps')->insertGetId([
                    'g_departament' => $gDepName,
                    'g_descripcion' => 'Categoría general que agrupa productos de ' . $gDepName,
                    'fkl_id_tienda' => $store->id_store,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_general_dep');

                // 2. Crear los departamentos específicos ligados a la generalización
                foreach ($possibleDeps[$gDepName] as $depName) {
                    $departmentId = DB::table('departments')->insertGetId([
                        'department' => $depName,
                        'description' => 'Subcategoría: ' . $depName,
                        'fk1_id_general_dep' => $gDepartmentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], 'id_department');

                    // 3. Simular montos de proveedores para el departamento (1 o 2 proveedores)
                    $depSuppliers = $suppliers->random(rand(1, 2));
                    foreach ($depSuppliers as $supplier) {
                        DB::table('department_suppliers')->insert([
                            'montos' => rand(10000, 50000),
                            'fk1_id_supplier' => $supplier->id_supplier,
                            'fk2_id_department' => $departmentId,
                        ]);
                        
                        // 4. Registrar pagos simulados a ese proveedor
                        DB::table('supplier_payments')->insert([
                            'fk1_id_supplier' => $supplier->id_supplier,
                            'fk2_id_department' => $departmentId,
                            'amount_paid' => rand(1000, 15000),
                            'payment_date' => $faker->dateTimeBetween('-2 months', 'now'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
