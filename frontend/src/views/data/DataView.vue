<script setup lang="ts">
import {ref, computed, watch} from 'vue';
import Employees from '@/components/data/employees/Employees.vue';
import Stores from '@/components/data/stores/Stores.vue';
import GeneralSales from '@/components/data/sales/GeneralSales.vue';
import StoresSales from '@/components/data/sales/StoresSales.vue';
import Departments from '@/components/data/departments/Departments.vue';
import Suppliers from '@/components/data/suppliers/Suppliers.vue';
const componentsMap = {
    'sucursal': Stores,
    'empleados': Employees,
    'ventas': GeneralSales,
    'ventas_sucursal': StoresSales,
    'departamentos': Departments,
    'proveedores': Suppliers,
    
};

const options = [
    { value: 'ventas', label: 'Ventas Generales' },
    { value: 'sucursal', label: 'Sucursales' },
    { value: 'empleados', label: 'Empleados' },
    { value: 'ventas_sucursal', label: 'Ventas por Sucursal' },
    { value: 'departamentos', label: 'Departamentos' },
    { value: 'proveedores', label: 'Proveedores' },

];

const selectedOption = ref(localStorage.getItem('dataViewOption') || 'ventas');

watch(selectedOption, (newValue: string) => {
    localStorage.setItem('dataViewOption', newValue);
});

const activeComponent = computed(() => {
    return (componentsMap as Record<string, any>)[selectedOption.value] || null;
});
</script>

<template>
    <div class="data-view">
        <h1>Datos del Negocio</h1>
        <p>Seleccione la opcion que desea Visualizar</p>
        <section class="component-section">
            <label class="component-label">{{ activeComponent?.label }}</label>
            <select
            class="component-select" 
            v-model="selectedOption">
                <option v-for="option in options" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
        </section>
        <section class="component-view">
           <KeepAlive class="component-container">
                <component :is="activeComponent" />
           </KeepAlive>
        </section>
    </div>
</template>

<style src="@/assets/styles/data/dataview.css" scoped>
</style>