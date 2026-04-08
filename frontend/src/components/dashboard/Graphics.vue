<script setup lang="ts">

import StoresServices from '@/services/StoresServices';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import ErrorMessage from '@/components/shared/Error.vue';
import type { ChartData, ChartOptions } from 'chart.js';
import BarGraph from '@/components/dashboard/graphics/BarGraph.vue';

// Variables para la gráfica general
const message = ref('');
const isValid = ref(false);
const colors = [
    { bg: 'rgba(255, 99, 132, 0.5)', border: 'rgba(255, 99, 132, 1)' },
    { bg: 'rgba(54, 162, 235, 0.5)', border: 'rgba(54, 162, 235, 1)' },
    { bg: 'rgba(255, 206, 86, 0.5)', border: 'rgba(255, 206, 86, 1)' },
    { bg: 'rgba(75, 192, 192, 0.5)', border: 'rgba(75, 192, 192, 1)' },
    { bg: 'rgba(153, 102, 255, 0.5)', border: 'rgba(153, 102, 255, 1)' },
    { bg: 'rgba(255, 159, 64, 0.5)', border: 'rgba(255, 159, 64, 1)' }
];

const chartData = ref<ChartData<'bar'>>({
  datasets: []
})

const chartOptions = ref<ChartOptions<'bar'>>({
  responsive: true,
  maintainAspectRatio: false, 
  plugins: {
    legend: { position: 'bottom' },
    title: { display: true, text: 'Ingresos Totales Por Departamento' }
  }
})

// Variables para las gráficas por sucursal
const message2 = ref('');
const isValid2 = ref(false);
const storeCharts = ref<Array<{
  storeId: number;
  storeName: string;
  chartData: ChartData<'bar'>;
  chartOptions: ChartOptions<'bar'>;
}>>([]);

onMounted(async () => {
    // Cargar gráfica general
    try {
        const response = await StoresServices.getSalesByGeneralDepartment();  
        const data = response.data.data.map((item: { general_department: string; total_sales: number; }) => ({
            department: item.general_department,
            totalSales: item.total_sales
        }));
        chartData.value = {
            labels: data.map((item: { department: string }) => item.department),
            datasets: [
                {
                label: 'Ingresos Totales',
                data: data.map((item: { totalSales: number }) => item.totalSales),
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
                }
            ]
        }
        isValid.value = false;
    } catch (error) {
        isValid.value = true;
        if(axios.isAxiosError(error)){
            const status = error.response?.status;
            if(status === 404){
                message.value = error.response?.data?.message;
            }
        }
    }

    // Cargar gráficas por sucursal
    try {
        const storesResponse = await StoresServices.getStores();
        const stores = storesResponse.data.data;

        // Crear una gráfica por cada sucursal
        for (let i = 0; i < stores.length; i++) {
            const store = stores[i];
            try {
                const response = await StoresServices.getSalesByDepartmentByStore(store.id);
                const data = response.data.data.map((item: { department: string; total_sales: number; }) => ({
                    department: item.department,
                    totalSales: item.total_sales
                }));
                

                const colorIndex = i % colors.length;
                
                storeCharts.value.push({
                    storeId: store.id,
                    storeName: store.name,
                    chartData: {
                        labels: data.map((item: { department: string }) => item.department),
                        datasets: [
                            {
                                label: 'Ingresos Totales',
                                data: data.map((item: { totalSales: number }) => item.totalSales),
                                backgroundColor: colors[colorIndex]!.bg,
                                borderColor: colors[colorIndex]!.border,
                                borderWidth: 1
                            }
                        ]
                    },
                    chartOptions: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            title: { display: true, text: `Ventas por Departamento - ${store.name}` }
                        }
                    }
                });
            } catch (error) {
                console.error(`Error al cargar datos de la sucursal ${store.name}:`, error);
            }
        }
        isValid2.value = storeCharts.value.length === 0;
        if (isValid2.value) {
            message2.value = 'No se encontraron datos de ventas por sucursal';
        }
    } catch (error) {
        isValid2.value = true;
        if(axios.isAxiosError(error)){
            const status = error.response?.status;
            if(status === 404){
                message2.value = error.response?.data?.message || 'No se encontraron sucursales';
            } else {
                message2.value = 'Error al cargar las sucursales';
            }
        }
    }
});

</script>

<template>
    <div class="graphics">
        <section class="graphics-stores-departments">

            <section class="general-graphics">
                <h2>Resumen General de Ventas - Todas las Sucursales</h2>
                <ErrorMessage v-if="isValid"
                    tittle="Error al cargar las gráficas de ventas"
                    :message="message || 'Hubo un error al obtener los datos. Por favor, inténtalo de nuevo más tarde.'"
                />
                <BarGraph v-else :chartData="chartData" :chartOptions="chartOptions" />
            </section>

            <section class="graphics-for-stores">
                <h2>Ventas por Departamento según Sucursal</h2>
                <ErrorMessage v-if="isValid2"
                    tittle="Error al cargar las gráficas por sucursal"
                    :message="message2 || 'Hubo un error al obtener los datos. Por favor, inténtalo de nuevo más tarde.'"
                />
                <div v-else class="store-charts-container">
                    <div v-for="chart in storeCharts" :key="chart.storeId" class="store-chart">
                        <BarGraph :chartData="chart.chartData" :chartOptions="chart.chartOptions" />
                    </div>
                </div>
            </section>

        </section>
        
    </div>
</template>

<style scoped>
@import "@/assets/styles/dashboard/componentes/graphics.css";
</style>