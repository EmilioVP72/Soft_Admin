<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $stores = DB::table('stores')->orderBy('id_store')->get();
        $suppliers = DB::table('suppliers')->get();
        
        if ($stores->isEmpty() || $suppliers->isEmpty()) {
            $this->command->warn('Stores or Suppliers missing. Skipping DepartmentSeeder.');
            return;
        }

        // Definiciones de departamentos basadas en la lógica real (generalización vs especificidad)
        $definitions = [
            0 => [ // Primera tienda (Tienda Matriz por ejemplo)
                'Lácteos' => [
                    ['name' => 'Lácteos', 'desc' => 'Leche, yogures y crema']
                ],
                'Farmacia' => [
                    ['name' => 'Farmacia', 'desc' => 'Medicamentos de patente, genéricos y cuidado personal']
                ],
                'Abarrotes' => [
                    ['name' => 'Abarrotes', 'desc' => 'Despensa básica y abarrotes generales']
                ],
            ],
            1 => [ // Segunda tienda (Sucursal Roma por ejemplo)
                'Lácteos' => [
                    ['name' => 'Quesos', 'desc' => 'Variedad de quesos regionales e importados']
                ],
                'Botanas' => [
                    ['name' => 'Botanas', 'desc' => 'Frituras, cacahuates y snacks salados'],
                    ['name' => 'Dulcería', 'desc' => 'Golosinas, chocolates y chicles']
                ]
            ]
        ];

        foreach ($stores as $index => $store) {
            // Utilizamos las definiciones preparadas. Si hubiera más tiendas, asignarían un por defecto.
            $storeDefs = $definitions[$index] ?? [
                'Abarrotes' => [
                    ['name' => 'Abarrotes', 'desc' => 'Artículos de primera necesidad']
                ],
                'Lácteos' => [
                    ['name' => 'Lácteos', 'desc' => 'Productos derivados de la leche']
                ]
            ];

            foreach ($storeDefs as $gDepName => $deps) {
                // 1. Crear la generalización (general_deps) para la tienda
                $gDepartmentId = DB::table('general_deps')->insertGetId([
                    'g_departament' => $gDepName,
                    'g_descripcion' => 'Categoría general que agrupa productos de ' . $gDepName,
                    'fkl_id_tienda' => $store->id_store,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_general_dep');

                // 2. Crear los departamentos específicos ligados a la generalización
                foreach ($deps as $dep) {
                    $departmentId = DB::table('departments')->insertGetId([
                        'department' => $dep['name'],
                        'description' => $dep['desc'],
                        'fk1_id_general_dep' => $gDepartmentId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], 'id_department');

                    // 3. Simular montos de proveedores para el departamento
                    $supplier = $suppliers->random();

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
                        'payment_date' => now()->subDays(rand(1, 45)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
