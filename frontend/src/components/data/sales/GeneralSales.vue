<script setup lang="ts">
import StoresServices from '@/services/StoresServices';
import { onMounted, ref } from 'vue';
const salesData = ref<Array<{ department: string; totalQuantity: number; totalSales: number }>>([]);
onMounted(async () => {
    try {
        const response = await StoresServices.getSalesByDepartment();
        salesData.value = response.data.data.map((sale: { department: string; total_quantity: number; total_sales: number }) => ({
            department: sale.department,
            totalQuantity: sale.total_quantity,
            totalSales: sale.total_sales
        }));
    } catch (error) {
        console.error('Error fetching general sales data:', error);
    }
});
</script>

<template>
    <div class="data-view">
        <h1>Ventas Generales</h1>
        <section class="table-section">
            <table class="sales-table">
                <thead class="table-header">
                    <tr class="header-row">
                        <th class="header-cell">Departamento</th>
                        <th class="header-cell">Cantidad Vendida</th>
                        <th class="header-cell">Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <tr v-for="sale in salesData" :key="sale.department" class="data-row">
                        <td class="data-cell">{{ sale.department }}</td>
                        <td class="data-cell">{{ sale.totalQuantity }}</td>
                        <td class="data-cell">$ {{ sale.totalSales }}</td>
                    </tr>
                    
                </tbody>
            </table>

        </section>
    </div>
</template>

<style src="@/assets/styles/data/components/generalSales.css" scoped>
</style>