<script setup lang="ts">
import StoresServices from '@/services/StoresServices';
import SuppliersServices from '@/services/SuppliersServices';
import { computed, onMounted, ref } from 'vue';
import ErrorMessage from '@/components/shared/Error.vue';
import type { ChartData, ChartOptions } from 'chart.js';
import BarGraph from '@/components/dashboard/graphics/BarGraph.vue';
import { useNotification } from '@/composables/useNotification';
import ReportsServices from '@/services/ReportsServices';

const salesData = ref<Array<{ department: string; totalQuantity: number; totalSales: number }>>([]); 
const supplierWithMostPayments = ref<{ name: string; paymentsCount: number } | null>(null);
const chartData = ref<ChartData<'bar'>>({ datasets: [] });
const chartOptions = ref<ChartOptions<'bar'>>({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
        title: { display: true, text: 'Ingresos Totales Por Departamento' }
    }
});
const error_data = ref<boolean>(false);
const { showError } = useNotification();

const topDepartment = computed(() => {
    if (salesData.value.length === 0) return null;
    return salesData.value.reduce((max, current) =>
        Number(current.totalSales) > Number(max.totalSales) ? current : max
    );
});

const lowDepartment = computed(() => {
    if (salesData.value.length === 0) return null;
    return salesData.value.reduce((min, current) =>
        Number(current.totalSales) < Number(min.totalSales) ? current : min
    );
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-SV', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(Number(value) || 0);
};

onMounted(async () => {
    try {
        const response = await StoresServices.getSalesByDepartment();
        salesData.value = response.data.data.map((sale: { department: string; total_quantity: number; total_sales: number }) => ({
            department: sale.department,
            totalQuantity: sale.total_quantity,
            totalSales: sale.total_sales
        }));
    } catch (error) {
        error_data.value = true;
        return;
    }

    try {
        const response = await StoresServices.getSalesByGeneralDepartment();
        const generalSalesData = response.data.data.map((item: { general_department: string; total_sales: number }) => ({
            department: item.general_department,
            totalSales: item.total_sales
        }));

        chartData.value = {
            labels: generalSalesData.map((item: { department: string }) => item.department),
            datasets: [
                {
                    label: 'Ingresos Totales',
                    data: generalSalesData.map((item: { totalSales: number }) => item.totalSales),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        };
    } catch (error) {
        showError('Error al cargar gráfica', 'No se pudieron cargar los datos de la gráfica general de ventas.');
    }

    try {
        const suppliersResponse = await SuppliersServices.getAllSuppliers();
        const suppliersData = suppliersResponse.data.data || [];

        const suppliersWithPayments = await Promise.all(
            suppliersData.map(async (supplier: any) => {
                const paymentsResponse = await SuppliersServices.getAllPaymentsToSuppliers(supplier.id_supplier);
                const payments = paymentsResponse.data.data || [];

                return {
                    name: supplier.supplier,
                    paymentsCount: payments.length
                };
            })
        );

        if (suppliersWithPayments.length > 0) {
            supplierWithMostPayments.value = suppliersWithPayments.reduce((maxSupplier, currentSupplier) =>
                currentSupplier.paymentsCount > maxSupplier.paymentsCount ? currentSupplier : maxSupplier
            );
        }
    } catch (error) {
        showError('Error al cargar proveedor', 'No se pudo obtener el proveedor con más pagos.');
    }
});

function downloadPdf() {
    ReportsServices.openGeneralSalesPdf();
}

function dummyExcel() {
    ReportsServices.openGeneralSalesExcel();
}
</script>

<template>
    <ErrorMessage v-if="error_data"
        tittle="Error al cargar las ventas generales"
        message="Hubo un error al obtener los datos de ventas. Por favor, inténtalo de nuevo más tarde o contacta al soporte si el problema persiste."
    />
    <div v-else class="data-view">
        <div class="toolbar">
            <h1>Ventas Generales</h1>
            <div class="toolbar-actions">
                <button class="btn-print" @click="downloadPdf">Exportar PDF</button>
                <button class="btn-print" @click="dummyExcel">Exportar Excel</button>
            </div>
        </div>

        <section class="summary-cards">
            <article class="summary-card">
                <span class="summary-label">Departamento con más ventas</span>
                <strong class="summary-title">{{ topDepartment?.department || 'Sin datos' }}</strong>
                <p class="summary-value">{{ topDepartment ? formatCurrency(topDepartment.totalSales) : 'N/A' }}</p>
            </article>

            <article class="summary-card">
                <span class="summary-label">Departamento con menos ventas</span>
                <strong class="summary-title">{{ lowDepartment?.department || 'Sin datos' }}</strong>
                <p class="summary-value">{{ lowDepartment ? formatCurrency(lowDepartment.totalSales) : 'N/A' }}</p>
            </article>

            <article class="summary-card">
                <span class="summary-label">Proveedor con más pagos</span>
                <strong class="summary-title">{{ supplierWithMostPayments?.name || 'Sin datos' }}</strong>
                <p class="summary-value">{{ supplierWithMostPayments ? `${supplierWithMostPayments.paymentsCount} pagos` : 'N/A' }}</p>
            </article>
        </section>

        <section class="chart-section">
            <h2>Resumen General de Ventas - Todas las Sucursales</h2>
            <BarGraph :chartData="chartData" :chartOptions="chartOptions" />
        </section>

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