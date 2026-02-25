<script setup lang="ts">
import StoresServices from '@/services/StoresServices';
import { onMounted, ref } from 'vue';
const storeData = ref<Array<{ colony: string; exterior_number: number; interior_number: number; name: string; reference: string; street: string; }>>([]);
onMounted(async () => {
    try {
        const response = await StoresServices.getStores();
        storeData.value = response.data.data.map((store: { colony: string; exterior_number: number; interior_number: number; name: string; reference: string; street: string; }) => ({
            colony: store.colony,
            exterior_number: store.exterior_number,
            interior_number: store.interior_number,
            name: store.name,
            reference: store.reference,
            street: store.street
        }));
        console.log('Store Data:', storeData.value);
    } catch (error) {
        console.error('Error fetching store data:', error);
    }
});
</script>

<template>
    <div class="data-view">
        <h1>Datos de las Sucursales</h1>
        <table class="stores-table">
            <thead class="table-header">
                <tr class="header-row">
                    <th class="header-cell">Nombre</th>
                    <th class="header-cell">Calle</th>
                    <th class="header-cell">Número Exterior</th>
                    <th class="header-cell">Número Interior</th>
                    <th class="header-cell">Colonia</th>
                    <th class="header-cell">Referencia</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <tr v-for="store in storeData" :key="store.name" class="data-row">
                    <td class="data-cell">{{ store.name }}</td>
                    <td class="data-cell">{{ store.street }}</td>
                    <td class="data-cell">{{ store.exterior_number }}</td>
                    <td class="data-cell">{{ store.interior_number || 'S/N' }}</td>
                    <td class="data-cell">{{ store.colony }}</td>
                    <td class="data-cell">{{ store.reference }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style  scoped>
@import '@/assets/styles/data/components/stores.css';
</style>