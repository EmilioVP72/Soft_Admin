<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Store;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener tiendas existentes
        $stores = Store::all();
        
        if ($stores->isEmpty()) {
            $this->command->warn('No hay tiendas en la base de datos. Por favor, crea tiendas primero.');
            return;
        }

        $employees = [
            // Tienda 1 - Empleados
            [
                'full_name' => 'Juan Carlos García López',
                'email' => 'juan.garcia@sofadmin.com',
                'phone' => '555-0001',
                'document_type' => 'DNI',
                'document_number' => '12345678A',
                'position' => 'Manager',
                'salary' => 3500.00,
                'status' => 'Active',
                'hire_date' => '2023-01-15',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->first()->id_store ?? 1,
                'notes' => 'Gerente de tienda principal. Responsable de operaciones diarias.',
            ],
            [
                'full_name' => 'María Elena Rodríguez Pérez',
                'email' => 'maria.rodriguez@sofadmin.com',
                'phone' => '555-0002',
                'document_type' => 'DNI',
                'document_number' => '87654321B',
                'position' => 'Supervisor',
                'salary' => 2500.00,
                'status' => 'Active',
                'hire_date' => '2023-03-20',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->first()->id_store ?? 1,
                'notes' => 'Supervisora de ventas. Maneja inventario y atención al cliente.',
            ],
            [
                'full_name' => 'Carlos Mendez Sánchez',
                'email' => 'carlos.mendez@sofadmin.com',
                'phone' => '555-0003',
                'document_type' => 'DNI',
                'document_number' => '11223344C',
                'position' => 'Cashier',
                'salary' => 1800.00,
                'status' => 'Active',
                'hire_date' => '2023-05-10',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->first()->id_store ?? 1,
                'notes' => 'Cajero principal del turno matutino.',
            ],
            [
                'full_name' => 'Ana Patricia Morales Guerra',
                'email' => 'ana.morales@sofadmin.com',
                'phone' => '555-0004',
                'document_type' => 'DNI',
                'document_number' => '55667788D',
                'position' => 'Stock',
                'salary' => 1600.00,
                'status' => 'Active',
                'hire_date' => '2023-06-05',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->first()->id_store ?? 1,
                'notes' => 'Encargada de almacén. Organiza y controla inventario.',
            ],
            [
                'full_name' => 'Roberto Luis Fernández Castro',
                'email' => 'roberto.fernandez@sofadmin.com',
                'phone' => '555-0005',
                'document_type' => 'DNI',
                'document_number' => '99887766E',
                'position' => 'Sales',
                'salary' => 2000.00,
                'status' => 'Active',
                'hire_date' => '2023-07-12',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->first()->id_store ?? 1,
                'notes' => 'Agente de ventas. Excelente en relaciones con clientes.',
            ],

            // Tienda 2 - Empleados (si existe)
            [
                'full_name' => 'Sandra Alejandra Ruiz Medina',
                'email' => 'sandra.ruiz@sofadmin.com',
                'phone' => '555-0006',
                'document_type' => 'DNI',
                'document_number' => '33445566F',
                'position' => 'Manager',
                'salary' => 3400.00,
                'status' => 'Active',
                'hire_date' => '2023-02-01',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->count() > 1 ? $stores[1]->id_store : $stores->first()->id_store,
                'notes' => 'Gerente de sucursal norte.',
            ],
            [
                'full_name' => 'Miguel Ángel Torres Reyes',
                'email' => 'miguel.torres@sofadmin.com',
                'phone' => '555-0007',
                'document_type' => 'DNI',
                'document_number' => '77889900G',
                'position' => 'Cashier',
                'salary' => 1700.00,
                'status' => 'On Leave',
                'hire_date' => '2023-04-15',
                'end_date' => null,
                'fk_id_user' => null,
                'fk_id_store' => $stores->count() > 1 ? $stores[1]->id_store : $stores->first()->id_store,
                'notes' => 'En licencia médica hasta fin de mes.',
            ],
            [
                'full_name' => 'Daniela Sofía Ramírez López',
                'email' => 'daniela.ramirez@sofadmin.com',
                'phone' => '555-0008',
                'document_type' => 'DNI',
                'document_number' => '44556677H',
                'position' => 'Sales',
                'salary' => 2100.00,
                'status' => 'Inactive',
                'hire_date' => '2022-11-20',
                'end_date' => '2024-01-31',
                'fk_id_user' => null,
                'fk_id_store' => $stores->count() > 1 ? $stores[1]->id_store : $stores->first()->id_store,
                'notes' => 'Empleada separada del sistema. Reasignada a otra empresa.',
            ],
        ];

        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        $this->command->info('✅ ' . count($employees) . ' empleados creados exitosamente.');
    }
}
