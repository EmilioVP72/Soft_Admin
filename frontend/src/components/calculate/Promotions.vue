<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import type { CalculatePromotion, TotalySalesByPromotion } from '@/interfaces/CalculateInterfaces';
import SuppliersServices from '@/services/SuppliersServices';
import { useNotification } from '@/composables/useNotification';
import { formatDateOnly } from '@/utils/datetime';

const formInput = ref({
    date: '',
    totaly_sales: 0
});

const dataList = ref<CalculatePromotion[]>([]);
const suppliers = ref<any[]>([]);
const selectedSupplier = ref('');
const error = ref(false);
const { showWarning, showError } = useNotification();

const calculateTable = computed<TotalySalesByPromotion[]>(() => {
    let temporalTotaly = 0;
    return dataList.value.map(item => {
        temporalTotaly += item.totaly_sales;
        return {
            ...item,
            acumulated_sales: temporalTotaly
        };
    });
});

const addRow = () => {
    if (!formInput.value.date || formInput.value.totaly_sales <= 0) {
        showWarning('Datos Inválidos', 'Por favor, ingresa una fecha y un valor de ventas válido.');
        return;
    }

    dataList.value.push({
        id: crypto.randomUUID(),
        date: formInput.value.date,
        totaly_sales: formInput.value.totaly_sales
    });

    formInput.value.date = '';
    formInput.value.totaly_sales = 0;
};

onMounted( async() => {
    try {
        const response = await SuppliersServices.getAllSuppliers();
        suppliers.value = response.data.data.map((supplier: any) => {
            return {
                id: supplier.id_supplier,
                name: supplier.supplier
            };
        }).sort((a: any, b: any) => Number(a.id || 0) - Number(b.id || 0));
    } catch (error) {
        showError('Error', 'No se pudieron cargar los proveedores.');
    }
});
</script>

<template>
    
    <div class="promotions-view">
        <h1>Calculadora de Promociones</h1>

        <section class="supplier-section">
            <label for="supplier" class="label-supplier">Proveedor:</label>
            <select name="supplier" id="supplier" v-model="selectedSupplier" class="select-supplier">
                <option value="">Seleccionar Proveedor</option>
                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                    {{ supplier.name }}
                </option>
            </select>
        </section>

        <div class="form-section">
            <h2>Agregar Registro de Ventas</h2>

            <form @submit.prevent="addRow">
                <div>
                    <label>Fecha</label>
                    <input
                        v-model="formInput.date"
                        type="date"
                    />
                </div>

                <div>
                    <label>Total Ventas ($)</label>
                    <input
                        v-model.number="formInput.totaly_sales"
                        type="number"
                        min="0"
                        step="0.01"
                    />
                </div>

                <button type="submit">Insertar</button>
            </form>
        </div>

        <div class="table-section">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total Ventas</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="calculateTable.length === 0">
                        <td colspan="2">No hay datos. Inserta un registro arriba.</td>
                    </tr>

                    <tr v-for="row in calculateTable" :key="row.id">
                        <td>{{ formatDateOnly(row.date) }}</td>
                        <td>$ {{ row.totaly_sales.toFixed(2) }}</td>
                    </tr>
                </tbody>

                <tfoot v-if="calculateTable.length > 0">
                    <tr>
                        <td>Total Acumulado</td>
                        <td>$ {{ calculateTable[calculateTable.length - 1]?.acumulated_sales?.toFixed(2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="actions">
            <button disabled>Exportar PDF</button>
            <button disabled>Exportar Excel</button>
        </div>
    </div>
</template>

<style src="@/assets/styles/calculate/components/promotions.css" scoped>
</style>