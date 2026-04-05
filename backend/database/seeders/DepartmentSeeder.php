<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $store = DB::table('stores')->first();
        $supplier = DB::table('suppliers')->first();
        
        if (!$store || !$supplier) {
            $this->command->warn('Store or Supplier missing. Skipping DepartmentSeeder.');
            return;
        }

        $gDepartmentId1 = DB::table('general_deps')->insertGetId([
            'g_departament' => 'Abarrotes',
            'g_descripcion' => 'Departamento de abarrotes en general',
            'fkl_id_tienda' => $store->id_store,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_general_dep');

        $gDepartmentId2 = DB::table('general_deps')->insertGetId([
            'g_departament' => 'Limpieza',
            'g_descripcion' => 'Productos de limpieza para hogar',
            'fkl_id_tienda' => $store->id_store,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_general_dep');

        $departmentId1 = DB::table('departments')->insertGetId([
            'department' => 'Lácteos',
            'description' => 'Leche, quesos, embutidos',
            'fk1_id_general_dep' => $gDepartmentId1,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_department');

        $departmentId2 = DB::table('departments')->insertGetId([
            'department' => 'Detergentes',
            'description' => 'En polvo, líquidos, suavizantes',
            'fk1_id_general_dep' => $gDepartmentId2,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_department');

        DB::table('department_suppliers')->insert([
            [
                'montos' => 50000.00,
                'fk1_id_supplier' => $supplier->id_supplier,
                'fk2_id_department' => $departmentId1,
            ],
            [
                'montos' => 20000.00,
                'fk1_id_supplier' => $supplier->id_supplier,
                'fk2_id_department' => $departmentId2,
            ]
        ]);
        
        DB::table('supplier_payments')->insert([
            [
                'fk1_id_supplier' => $supplier->id_supplier,
                'fk2_id_department' => $departmentId1,
                'amount_paid' => 15000.00,
                'payment_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
