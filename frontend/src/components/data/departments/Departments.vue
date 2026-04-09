<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import DepartmentsServices from '@/services/DepartmentsServices';
import StoresServices from '@/services/StoresServices';
import DepartmentsForm from './DepartmentsForm.vue';
import ConfirmModal from '@/components/shared/ConfirmModal.vue';
import { useNotification } from '@/composables/useNotification';
import ReportsServices from '@/services/ReportsServices';

const departments = ref<any[]>([]);
const stores = ref<any[]>([]);
const selectedStoreId = ref<number | ''>('');

const showModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedDepartment = ref<any>(null);
const { showWarning, showError } = useNotification();

const loadStores = async () => {
    try {
        const response = await StoresServices.getStores();
        stores.value = [...(response.data.data || response.data || [])].sort((a: any, b: any) =>
            Number(a.id_store || a.id || 0) - Number(b.id_store || b.id || 0)
        );
    } catch (error) {
        showError('Error al cargar', 'No se pudieron cargar las tiendas.');
    }
};

const loadDepartments = async () => {
    if (!selectedStoreId.value) {
        departments.value = [];
        return;
    }
    try {
        const response = await DepartmentsServices.getDepartmentsByStore(Number(selectedStoreId.value));
        departments.value = [...(response.data.data || response.data || [])].sort((a: any, b: any) =>
            Number(a.id_department || a.id || 0) - Number(b.id_department || b.id || 0)
        );
    } catch (error) {
        showError('Error al cargar', 'No se pudieron cargar los departamentos.');
    }
};

watch(selectedStoreId, () => {
    loadDepartments();
});

onMounted(() => {
    loadStores();
});

const openAddModal = () => {
    if (!selectedStoreId.value) {
        showWarning('Selección Requerida', 'Primero selecciona una tienda para poder agregarle un departamento.');
        return;
    }
    selectedDepartment.value = null;
    showModal.value = true;
};

const openEditModal = (department: any) => {
    selectedDepartment.value = department;
    showModal.value = true;
};

const handleSaved = () => {
    showModal.value = false;
    loadDepartments();
};

const openDeleteConfirm = (department: any) => {
    selectedDepartment.value = department;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!selectedDepartment.value) return;
    try {
        await DepartmentsServices.deleteDepartment(selectedDepartment.value.id_department);
        showDeleteConfirm.value = false;
        loadDepartments();
    } catch (error) {
        showError('Error', 'No se pudo eliminar el departamento.');
    }
};

const getSelectedStoreName = () => {
    if (!selectedStoreId.value) return 'Todas las Sucursales';
    const store = stores.value.find(s => String(s.id_store) === String(selectedStoreId.value));
    return store ? store.name : 'Sucursal Desconocida';
};

const exportPdf = async () => {
    if (!selectedStoreId.value) {
        showWarning('Filtro Requerido', 'Selecciona una tienda para poder exportar los departamentos.');
        return;
    }
    try {
        await ReportsServices.exportDynamicDepartmentsPdf(departments.value, getSelectedStoreName());
    } catch (error) {
        showError('Error', 'Fallo al generar el reporte PDF.');
    }
};

const exportExcel = async () => {
    if (!selectedStoreId.value) {
        showWarning('Filtro Requerido', 'Selecciona una tienda para poder exportar los departamentos.');
        return;
    }
    try {
        await ReportsServices.exportDynamicDepartmentsExcel(departments.value, getSelectedStoreName());
    } catch (error) {
        showError('Error', 'Fallo al generar el reporte Excel.');
    }
};
</script>

<template>
    <div class="container">
        <section class="header-container">
            <h1>Departamentos</h1>
            <div class="header-buttons">
                <button class="btn-new" @click="openAddModal">Nuevo Departamento</button>
                <button class="btn-pdf" @click="exportPdf" :disabled="!selectedStoreId">Exportar a PDF</button>
                <button class="btn-excel" @click="exportExcel" :disabled="!selectedStoreId">Exportar a Excel</button>
            </div>
        </section>
        
        <section class="filter-container">
            <label for="storeSelect">Selecciona una Tienda: </label>
            <select id="storeSelect" v-model="selectedStoreId" class="store-select">
                <option value="" disabled>-- Seleccione una tienda --</option>
                <option v-for="store in stores" :key="store.id_store" :value="store.id_store">
                    {{ store.name }}
                </option>
            </select>
        </section>

        <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Departamento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="departments.length === 0">
                        <td colspan="3" class="text-center">Seleccione una tienda para ver sus departamentos</td>
                    </tr>
                    <tr v-for="department in departments" :key="department.id_department">
                        <td>{{ department.id_department }}</td>
                        <!-- Depending on what the API returns. I'll assume department.department or department.name -->
                        <td>{{ department.department || department.name }}</td>
                        <td>
                            <button class="btn-edit" @click="openEditModal(department)">Editar</button>
                            <button class="btn-delete" @click="openDeleteConfirm(department)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- Modal Agregar/Editar -->
        <DepartmentsForm 
            v-if="showModal" 
            :department="selectedDepartment"
            :storeId="selectedStoreId"
            @close="showModal = false" 
            @saved="handleSaved" 
        />

        <!-- Alerta Eliminar -->
        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Eliminar departamento"
            :message="`¿Estás seguro que deseas eliminar permanentemente el departamento '${selectedDepartment?.department || selectedDepartment?.name}'?`"
            confirmLabel="Sí, eliminar"
            cancelLabel="Cancelar"
            :danger="true"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
        />
    </div>
</template>

<style scoped>
@import '@/assets/styles/data/components/departments.css';

.filter-container {
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.store-select {
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #ccc;
    font-size: 1rem;
    min-width: 250px;
}
.text-center {
    text-align: center;
    padding: 1.5rem;
    color: #666;
}
</style>