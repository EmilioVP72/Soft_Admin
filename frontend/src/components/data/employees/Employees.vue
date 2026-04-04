<script setup lang="ts">
// Seccion: "Importaciones"
// Explicacion: Se importan las dependencias de Vue, el servicio de empleados,
//              el formulario modal y el modal de confirmacion de eliminacion
import { ref, onMounted } from 'vue';
import EmployeeServices from '../../../services/EmployeeServices';
import EmployeeForm from './EmployeeForm.vue';
import ConfirmModal from '../../shared/ConfirmModal.vue';
import ErrorMessage from '@/components/shared/Error.vue';

// Seccion: "Estado reactivo"
// Explicacion: Variables que controlan la lista de empleados, la visibilidad del formulario
//              y el estado del modal de confirmacion antes de eliminar
const employeeData = ref<any[]>([]);
const showForm = ref(false);
const selectedEmployeeId = ref<number | undefined>(undefined);
const showConfirm = ref(false);
const pendingDeleteId = ref<number | null>(null);
const pendingDeleteName = ref('');

// Seccion: "Carga de datos"
// Explicacion: Llama al endpoint para obtener todos los empleados, mapea la respuesta
//              al formato que usa la tabla y los ordena por ID ascendente
async function fetchEmployees() {
    try {
        const response = await EmployeeServices.getEmployees();
        employeeData.value = response.data.data.map((employee: any) => ({
            id: employee.id,
            full_name: employee.full_name,
            email: employee.email,
            phone: employee.phone,
            position: employee.position,
            salary: employee.salary,
            status: employee.status,
            hire_date: employee.hire_date,
            exit_date: employee.exit_date,
            end_date: employee.end_date,
            store: {
                store: employee.store.store,
                colony: employee.store.colony,
                street: employee.store.street
            }
        })).sort((a: any, b: any) => a.id - b.id);
    } catch (error) {
        error_data.value = true;
    }
}

// Seccion: "Control del formulario"
// Explicacion: openInsert abre el form en modo alta (sin ID); openEdit lo abre en modo
//              edicion pasando el ID del empleado seleccionado
function openInsert() {
    selectedEmployeeId.value = undefined;
    showForm.value = true;
}

function openEdit(id: number) {
    selectedEmployeeId.value = id;
    showForm.value = true;
}

// Seccion: "Control de eliminacion"
// Explicacion: onDelete guarda el ID y nombre del empleado a eliminar y abre el modal
//              de confirmacion; confirmDelete ejecuta el borrado y recarga la tabla;
//              cancelDelete cierra el modal sin hacer cambios
function onDelete(id: number, name: string) {
    pendingDeleteId.value = id;
    pendingDeleteName.value = name;
    showConfirm.value = true;
}

async function confirmDelete() {
    if (pendingDeleteId.value === null) return;
    try {
        await EmployeeServices.deleteEmployee(pendingDeleteId.value);
        await fetchEmployees();
    } catch (error) {
        console.error('Error al eliminar empleado:', error);
    } finally {
        showConfirm.value = false;
        pendingDeleteId.value = null;
        pendingDeleteName.value = '';
    }
}

function cancelDelete() {
    showConfirm.value = false;
    pendingDeleteId.value = null;
    pendingDeleteName.value = '';
}

// Seccion: "Callbacks del formulario"
// Explicacion: onSaved cierra el formulario y recarga la tabla tras guardar con exito;
//              onCancel simplemente cierra el formulario sin cambios
async function onSaved() {
    showForm.value = false;
    await fetchEmployees();
}

function onCancel() {
    showForm.value = false;
}

// Seccion: "Impresion"
// Explicacion: Abre el reporte PDF generado por el backend en una nueva pestaña
function printReport() {
    window.open('http://localhost:8000/api/reports/employees', '_blank');
}

// Seccion: "Inicializacion"
// Explicacion: Al montar el componente se cargan los empleados automaticamente
onMounted(fetchEmployees);
</script>

<template>
    <ErrorMessage v-if="error_data"
        tittle="Error al cargar los datos de empleados"
        message="Hubo un error al obtener los datos. Por favor, inténtalo de nuevo más tarde o contacta al soporte si el problema persiste."
    />
    <div v-else class="data-view">
        <div class="toolbar">
            <h1>Datos de los Empleados</h1>
            <div class="toolbar-actions">
                <button class="btn-new" @click="openInsert">+ Nuevo Empleado</button>
                <button class="btn-print" @click="printReport">Imprimir Reporte</button>
            </div>
        </div>

        <EmployeeForm
            v-if="showForm"
            :employee-id="selectedEmployeeId"
            @saved="onSaved"
            @cancel="onCancel"
        />

        <ConfirmModal
            v-if="showConfirm"
            :message="`¿Estás seguro de que deseas eliminar al empleado ${pendingDeleteName}? Esta acción no se puede deshacer.`"
            title="Eliminar Empleado"
            confirm-label="Eliminar"
            :danger="true"
            @confirm="confirmDelete"
            @cancel="cancelDelete"
        />

        <div class="table-section">
        <table class="stores-table">
            <thead class="table-header">
                <tr class="header-row">
                    <th class="header-cell">No.</th>
                    <th class="header-cell">Nombre Completo</th>
                    <th class="header-cell">email</th>
                    <th class="header-cell">Telefono</th>
                    <th class="header-cell">Puesto</th>
                    <th class="header-cell">Salario</th>
                    <th class="header-cell">Sucursal</th>
                    <th class="header-cell">Estado</th>
                    <th class="header-cell">Fecha de Ingreso</th>
                    <th class="header-cell">Fecha de Salida</th>
                    <th class="header-cell">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <tr v-for="employee in employeeData" :key="employee.id" class="data-row">
                    <td class="data-cell">{{ employee.id }}</td>
                    <td class="data-cell">{{ employee.full_name }}</td>
                    <td class="data-cell">{{ employee.email }}</td>
                    <td class="data-cell">{{ employee.phone }}</td>
                    <td class="data-cell">{{ employee.position }}</td>
                    <td class="data-cell">$ {{ employee.salary }}</td>
                    <td class="data-cell">{{ employee.store.store }}</td>
                    <td class="data-cell">{{ employee.status }}</td>
                    <td class="data-cell">{{ employee.hire_date }}</td>
                    <td class="data-cell">{{ employee.end_date }}</td>
                    <td class="data-cell">
                        <button class="action-button edit-button" @click="openEdit(employee.id)">Editar</button>
                        <button class="action-button delete-button" @click="onDelete(employee.id, employee.full_name)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</template>

<style src="@/assets/styles/data/components/employees.css" scoped>
</style>