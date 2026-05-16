<script setup lang="ts">
import { onMounted, ref } from "vue";
import GeneralDepartmentsForm from "./GeneralDepartmentsForm.vue";
import ConfirmModal from '@/components/shared/ConfirmModal.vue';
import DepartmentsServices from "@/services/DepartmentsServices";
import { useNotification } from '@/composables/useNotification';
import ReportsServices from "@/services/ReportsServices";

const generalDepartments = ref<any[]>([]);
const showForm = ref(false);
const isEditForm = ref(false);
const showDeleteConfirm = ref(false);
const selectedDepartment = ref<any>(null);

const { showError, showSuccess } = useNotification();

onMounted(async () => {
    await getGeneralDepartments();
});

const getGeneralDepartments = async () => {
    try {
        const response = await DepartmentsServices.getAllGeneralDepartments();
        generalDepartments.value = response.data.data.map((department: any) => {
            return {
                id: department.id_general_dep,
                name: department.g_departament,
                description: department.g_descripcion,
            };
        }).sort((a: any, b: any) => a.id - b.id);
    } catch (error) {
        showError('Error al cargar', 'No se pudieron cargar los departamentos generales.');
    }
};

const addGeneralDepartment = () => {
    selectedDepartment.value = null;
    isEditForm.value = false;
    showForm.value = true;
};

const editGeneralDepartment = (department: any) => {
    selectedDepartment.value = department;
    isEditForm.value = true;
    showForm.value = true;
};

const deleteGeneralDepartment = (department: any) => {
    selectedDepartment.value = department;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!selectedDepartment.value) return;
    try {
        await DepartmentsServices.deleteGeneralDepartment(selectedDepartment.value.id);
        showSuccess('Eliminado', 'El departamento general se ha eliminado.');
        showDeleteConfirm.value = false;
        getGeneralDepartments();
    } catch (error) {
        showError('Error', 'No se pudo eliminar el departamento general.');
    }
};

const handleSaved = () => {
    showForm.value = false;
    getGeneralDepartments();
};

const exportPdf = async () => {
    try {
        await ReportsServices.exportDynamicDepartmentsPdf(generalDepartments.value, 'Departamentos Generales');
    } catch (error) {
        showError('Error', 'Fallo al generar el reporte PDF.');
    }
};

const exportExcel = async () => {
    try {
        await ReportsServices.exportDynamicDepartmentsExcel(generalDepartments.value, 'Departamentos Generales');
    } catch (error) {
        showError('Error', 'Fallo al generar el reporte Excel.');
    }
};
</script>

<template>
    <div class="container">
        <section class="header-container">
            <h1>Departamentos Generales</h1>
            <div class="header-buttons">
                <button class="btn-new" @click="addGeneralDepartment">Agregar Departamento</button>
                <button class="btn-pdf" @click="exportPdf">Exportar PDF</button>
                <button class="btn-excel" @click="exportExcel">Exportar Excel</button>
            </div>
        </section>

        <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="department in generalDepartments" :key="department.id">
                        <td>{{ department.id }}</td>
                        <td>{{ department.name }}</td>
                        <td>{{ department.description }}</td>
                        <td>
                            <button class="btn-edit" @click="editGeneralDepartment(department)">Editar</button>
                            <button class="btn-delete" @click="deleteGeneralDepartment(department)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <GeneralDepartmentsForm 
            v-if="showForm" 
            :isEditForm="isEditForm" 
            :department="selectedDepartment"
            @close="showForm = false" 
            @saved="handleSaved" 
        />

        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Eliminar departamento general"
            :message="`¿Estás seguro que deseas eliminar permanentemente el departamento general '${selectedDepartment?.name}'?`"
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
</style>
