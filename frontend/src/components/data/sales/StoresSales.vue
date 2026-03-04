<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import storesServices from '@/services/StoresServices';


const storesSales = ref<Array<{ department: string; totalQuantity: number; totalSales: number }>>([]);
const dataStore = ref<Array<{ storeId: number; storeName: string }>>([]);
var selectedOption = ref<number>(0);

// selectedStoreId es computed, se calcula automáticamente cuando selectedOption cambia
const selectedStoreId = computed(() => {
    return selectedOption.value;
});

// Watch para ver los cambios cuando seleccionas otra sucursal
watch(selectedOption, async (_newValue) => {
    try {
        const response = await storesServices.getSalesByDepartmentByStore(selectedStoreId.value);
        storesSales.value = response.data.data.map((sale: { department: string; total_quantity: number; total_sales: number }) => ({
            department: sale.department,
            totalQuantity: sale.total_quantity,
            totalSales: sale.total_sales
        }));
    } catch (error) {
        
    }
});

onMounted(async () => {
    try {
        const response = await storesServices.getStores();
        storesSales.value = response.data.data;
        dataStore.value = response.data.data.map((store: { id: number; name: string }) => ({ 
            storeId: store.id, 
            storeName: store.name 
        }));

        // Solo asigna a selectedOption, selectedStoreId se calcula automáticamente
        if (dataStore.value.length > 0) {
            selectedOption.value = dataStore.value[0]!.storeId;
        }

        

    } catch (error) {
        console.error('Error fetching stores sales:', error);
    }

    
});

</script>

<template>
    <div class="data-view">
        <h1>Ventas por Sucursal</h1>
        <section class="component-section">
            <label class="component-label">Seleccione la Sucursal</label>
            <select
            class="component-select" 
            v-model="selectedOption">
                <option v-for="option in dataStore" :key="option.storeId" :value="option.storeId">
                    {{ option.storeName }}
                </option>
            </select>
        </section>
        
        <section class="table-section">
            <thead class="table-header">
                <tr class="table-row">
                    <th class="table-cell">Departamento</th>
                    <th class="table-cell">Cantidad Vendida</th>
                    <th class="table-cell">Total de Ventas</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <tr v-for="sale in storesSales" :key="sale.department" class="table-row">
                    <td class="table-cell">{{ sale.department }}</td>
                    <td class="table-cell">{{ sale.totalQuantity }}</td>
                    <td class="table-cell">$ {{ sale.totalSales }}</td>
                </tr>
            </tbody>
        </section>
    </div>
</template>

<style src="@/assets/styles/data/components/storeSales.css" scoped>
</style>