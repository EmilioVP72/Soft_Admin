<script setup lang="ts">
// Seccion: "Importaciones"
// Explicacion: Se importan las dependencias de Vue, el servicio de empleados,
//              el formulario modal y el modal de confirmacion de eliminacion
import { ref, onMounted } from 'vue';
import EmployeeServices from '../../../services/EmployeeServices';
import EmployeeForm from './EmployeeForm.vue';
import { useNotification } from '@/composables/useNotification';
import ConfirmModal from '../../shared/ConfirmModal.vue';
import ErrorMessage from '@/components/shared/Error.vue';
import { normalizeSearchText } from '@/utils/search';
import { formatDateOnly, toDateInputValue } from '@/utils/datetime';

// Seccion: "Estado reactivo"
// Explicacion: Variables que controlan la lista de empleados, la visibilidad del formulario
//              y el estado del modal de confirmacion antes de eliminar
const employeeData = ref<any[]>([]);
const originalEmployeeData = ref<any[]>([]);
const showForm = ref(false);
const selectedEmployeeId = ref<number | undefined>(undefined);
const showConfirm = ref(false);
const pendingDeleteId = ref<number | null>(null);
const pendingDeleteName = ref('');
const error_data = ref(false);
const { showError } = useNotification();

const searchQuery = ref('');
const filterStore = ref('');
const filterStatus = ref('');
const filterPosition = ref('');
const filterSalaryMin = ref('');
const filterSalaryMax = ref('');
const filterHireDate = ref('');

const storesOptions = ref<string[]>([]);
const statusOptions = ref<string[]>([]);
const positionOptions = ref<string[]>([]);

// Seccion: "Carga de datos"
// Explicacion: Llama al endpoint para obtener todos los empleados, mapea la respuesta
//              al formato que usa la tabla y los ordena por ID ascendente
async function fetchEmployees() {
    try {
        const response = await EmployeeServices.getEmployees();
        const mappedEmployees = response.data.data.map((employee: any) => ({
            id: employee.id,
            full_name: employee.full_name,
            email: employee.email,
            phone: employee.phone,
            position: employee.position,
            salary: employee.salary,
            status: employee.status,
            hire_date_raw: employee.hire_date,
            hire_date: formatDateOnly(employee.hire_date),
            exit_date: employee.exit_date,
            end_date_raw: employee.end_date,
            end_date: formatDateOnly(employee.end_date),
            store: {
                store: employee.store.store,
                colony: employee.store.colony,
                street: employee.store.street
            }
        })).sort((a: any, b: any) => a.id - b.id);

        originalEmployeeData.value = mappedEmployees;
        storesOptions.value = Array.from(new Set(mappedEmployees.map((employee: any) => employee.store?.store).filter(Boolean)));
        statusOptions.value = Array.from(new Set(mappedEmployees.map((employee: any) => employee.status).filter(Boolean)));
        positionOptions.value = Array.from(new Set(mappedEmployees.map((employee: any) => employee.position).filter(Boolean)));
        applyFilters();
    } catch (error) {
        error_data.value = true;
    }
}

function applyFilters() {
    let result = [...originalEmployeeData.value];

    if (filterStore.value) {
        result = result.filter((employee: any) => employee.store?.store === filterStore.value);
    }

    if (filterStatus.value) {
        result = result.filter((employee: any) => employee.status === filterStatus.value);
    }

    if (filterPosition.value) {
        result = result.filter((employee: any) => employee.position === filterPosition.value);
    }

    if (filterSalaryMin.value !== '' || filterSalaryMax.value !== '') {
        result = result.filter((employee: any) => {
            const salary = Number(employee.salary) || 0;
            const min = filterSalaryMin.value !== '' ? Number(filterSalaryMin.value) : -Infinity;
            const max = filterSalaryMax.value !== '' ? Number(filterSalaryMax.value) : Infinity;
            return salary >= min && salary <= max;
        });
    }

    if (filterHireDate.value) {
        result = result.filter((employee: any) => {
            const normalizedDate = toDateInputValue(employee.hire_date_raw || employee.hire_date);
            if (!normalizedDate) return false;
            return normalizedDate === filterHireDate.value;
        });
    }

    const query = normalizeSearchText(searchQuery.value);
    if (query !== '') {
        result = result.filter((employee: any) => {
            const searchableValues = [
                employee.id,
                employee.full_name,
                employee.email,
                employee.phone,
                employee.position,
                employee.salary,
                employee.status,
                employee.hire_date,
                employee.end_date,
                employee.store?.store,
            ];

            return searchableValues.some((value: any) => {
                if (value === null || value === undefined) return false;
                return normalizeSearchText(value).includes(query);
            });
        });
    }

    employeeData.value = result;
}

function searchEmployees() {
    applyFilters();
}

function clearFilters() {
    searchQuery.value = '';
    filterStore.value = '';
    filterStatus.value = '';
    filterPosition.value = '';
    filterSalaryMin.value = '';
    filterSalaryMax.value = '';
    filterHireDate.value = '';
    applyFilters();
}

function translateEmployeeStatus(status: string) {
    const normalized = String(status || '').trim().toLowerCase();

    const statusMap: Record<string, string> = {
        active: 'Activo',
        inactive: 'Inactivo',
        terminated: 'Terminado',
        suspended: 'Suspendido',
        'on leave': 'En licencia',
        on_leave: 'En licencia',
        resigned: 'Renunció',
    };

    return statusMap[normalized] || status;
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
        showError('Error', 'No se pudo eliminar el empleado.');
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
                <div class="toolbar-groups">
                    <section class="filters-search">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="searchEmployees"
                            type="text"
                            class="form-control"
                            placeholder="Buscar"
                        >
                        <button class="btn-print" @click="searchEmployees">Buscar</button>
                        <button class="btn-print" @click="clearFilters">Limpiar</button>
                    </section>

                    <section class="filters-export">
                        <button class="btn-print" @click="printReport">Imprimir Reporte</button>
                    </section>

                    <section class="filters-select" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; flex-direction: column;">
                            <label for="employee-store-filter" style="color:white; font-size: 0.8rem;">Sucursal</label>
                            <select id="employee-store-filter" class="form-control" v-model="filterStore" @change="applyFilters">
                                <option value="">Todas</option>
                                <option v-for="store in storesOptions" :key="store" :value="store">{{ store }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="employee-status-filter" style="color:white; font-size: 0.8rem;">Estado</label>
                            <select id="employee-status-filter" class="form-control" v-model="filterStatus" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="employee-position-filter" style="color:white; font-size: 0.8rem;">Puesto</label>
                            <select id="employee-position-filter" class="form-control" v-model="filterPosition" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="position in positionOptions" :key="position" :value="position">{{ position }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Salario (Mín - Máx)</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="number" class="form-control" placeholder="Min" style="width: 80px;" v-model="filterSalaryMin" @input="applyFilters">
                                <input type="number" class="form-control" placeholder="Máx" style="width: 80px;" v-model="filterSalaryMax" @input="applyFilters">
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="employee-hire-date-filter" style="color:white; font-size: 0.8rem;">Fecha Ingreso</label>
                            <input id="employee-hire-date-filter" type="date" class="form-control" v-model="filterHireDate" @change="applyFilters">
                        </div>
                    </section>
                </div>

                <div class="toolbar-primary-action">
                    <button class="btn-new" @click="openInsert">+ Nuevo Empleado</button>
                </div>
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
                    <td class="data-cell">{{ translateEmployeeStatus(employee.status) }}</td>
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