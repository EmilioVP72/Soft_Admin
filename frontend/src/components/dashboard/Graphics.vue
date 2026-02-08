<script setup lang="ts">

import StoresServices from '@/services/StoresServices';
import { onMounted, ref } from 'vue';
import axios, { AxiosError } from 'axios';
import type { ChartData, ChartOptions } from 'chart.js';
import BarGraph from '@/components/dashboard/graphics/BarGraph.vue';


const salesByDepartment = ref([]);
const message = ref('');
const isValid = ref(false);

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

onMounted(async () => {
    try {
        const response = await StoresServices.getSalesByDepartment();        
        var departments = response.data.data.map((item: { department: string; }) => item.department);
        var sales = response.data.data.map((item: { totalSales: number; }) => item.totalSales);
        chartData.value = {
            labels: departments,
            datasets: [
                {
                label: 'Ingresos Totales',
                data: sales,
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
});

</script>

<template>
    <div class="graphics">
        <section class="graphics-stores-departments">

            <section class="general-graphics">
                <h2>Resumen General de Ventas - Todas las Sucursales</h2>
                <p v-if="isValid" class="error-message">{{ message }}</p>
                <BarGraph v-else :chartData="chartData" :chartOptions="chartOptions" />
                

            </section>

            <section class="graphics-for-stores">
                <h2>Ventas por Departamento según Sucursal</h2>
                
            </section>

        </section>
        
    </div>
</template>

<style scoped>
@import "@/assets/styles/dashboard/componentes/graphics.css";
</style>