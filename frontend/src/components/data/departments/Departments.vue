<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import DepartmentsServices from '@/services/DepartmentsServices';
import StoresServices from '@/services/StoresServices';
import DepartmentsForm from './DepartmentsForm.vue';
import ConfirmModal from '@/components/shared/ConfirmModal.vue';

const departments = ref<any[]>([]);
const stores = ref<any[]>([]);
const selectedStoreId = ref<number | ''>('');

const showModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedDepartment = ref<any>(null);

const loadStores = async () => {
    try {
        const response = await StoresServices.getStores();
        stores.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error al cargar tiendas:", error);
    }
};

const loadDepartments = async () => {
    if (!selectedStoreId.value) {
        departments.value = [];
        return;
    }
    try {
        const response = await DepartmentsServices.getDepartmentsByStore(Number(selectedStoreId.value));
        departments.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error al cargar departamentos:", error);
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
        alert("Primero selecciona una tienda para poder agregarle un departamento.");
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
        console.error("Error eliminando el departamento:", error);
    }
};
</script>

<template>
    <div class="container">
        <section class="header-container">
            <h1>Departamentos</h1>
            <div class="header-buttons">
                <button class="btn-new" @click="openAddModal">Nuevo Departamento</button>
                <button class="btn-pdf">Exportar a PDF</button>
                <button class="btn-excel">Exportar a Excel</button>
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