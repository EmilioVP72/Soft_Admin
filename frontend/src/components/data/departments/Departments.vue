<script setup lang="ts">
import { onMounted, ref } from 'vue';
// import DepartmentsServices from '@/services/DepartmentsServices';

const departments = ref<any[]>([]);

const loadData = async () => {
    try {
        // La llamada a la API está comentada para evitar errores, pero lista para usarse.
        // const response = await DepartmentsServices.getAllDepartments();
        // departments.value = response.data.data;
        
        // Datos mockeados para poder ver y ajustar la vista mientras se prepara la API.
        departments.value = [
            { id_department: 1, department: 'Recursos Humanos' },
            { id_department: 2, department: 'Ventas' },
            { id_department: 3, department: 'Finanzas' },
            { id_department: 4, department: 'Tecnología de la Información' },
        ];
    } catch (error) {
        console.log(error);
    }
};

onMounted(() => {
    loadData();
});

const openAddModal = () => {
    console.log("Abrir modal agregar departamento");
};

const openEditModal = (department: any) => {
    console.log("Editar departamento:", department);
};

const openDeleteConfirm = (department: any) => {
    console.log("Eliminar departamento:", department);
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
                    <tr v-for="department in departments" :key="department.id_department">
                        <td>{{ department.id_department }}</td>
                        <td>{{ department.department }}</td>
                        <td>
                            <button class="btn-edit" @click="openEditModal(department)">Editar</button>
                            <button class="btn-delete" @click="openDeleteConfirm(department)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</template>

<style scoped>
@import '@/assets/styles/data/components/departments.css';
</style>