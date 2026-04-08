<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import storesServices from '@/services/StoresServices';
import ErrorMessage from '@/components/shared/Error.vue';

const error_data = ref<boolean>(false);
const error_details = ref<string>('');
const storesSales = ref<any[]>([]);
const storesSalesByStore = ref<any[]>([]);
const dataStore = ref<Array<{ storeId: number; storeName: string }>>([]);
var selectedOption = ref<number>(Number(localStorage.getItem('storesSalesOption')) || 0);

// selectedStoreId es computed, se calcula automáticamente cuando selectedOption cambia
const selectedStoreId = computed(() => {
    return selectedOption.value;
});

// Watch para ver los cambios cuando seleccionas otra sucursal
watch(selectedOption, async (_newValue) => {
    localStorage.setItem('storesSalesOption', String(_newValue));
    try {
        const saleData = await storesServices.getSalesbyDepartmentForStore();
        
        let flattenedSales: any[] = [];
        (saleData.data.data || []).forEach((sale: any) => {
            const baseSale = {
                notes: sale.notes,
                payment: sale.payment, 
                store_name: sale.store_name || (sale.store && sale.store.name) || 'N/A',
                total_amount: sale.total_amount,
                transaction_date: sale.transaction_date,
                transaction_type: sale.transaction_type,
                user_name: sale.user_name || (sale.user && sale.user.name) || 'N/A',
            };
            
            if (sale.details && sale.details.length > 0) {
                sale.details.forEach((detail: any) => {
                    flattenedSales.push({
                        ...baseSale,
                        department: detail.department,
                        id_transaction_detail: detail.id_transaction_detail,
                        quantity: detail.quantity,
                        subtotal: detail.subtotal,
                        unit_price: detail.unit_price,
                    });
                });
            } else {
                flattenedSales.push({
                    ...baseSale,
                    department: 'N/A',
                    id_transaction_detail: null,
                    quantity: 0,
                    subtotal: 0,
                    unit_price: 0,
                });
            }
        });
        
        storesSales.value = flattenedSales;

        // selectedOption guarda el ID, pero necesitamos el nombre de la sucursal para filtrar
        const selectedStore = dataStore.value.find(s => s.storeId === selectedOption.value);
        const selectedStoreName = selectedStore ? selectedStore.storeName : '';

        storesSalesByStore.value = storesSales.value.filter(sale => sale.store_name === selectedStoreName);
        
        // Fallback: If filter is empty but there are sales, show all sales so we can see them.
        if (storesSalesByStore.value.length === 0 && storesSales.value.length > 0) {
            error_details.value = `Filter empty for "${selectedStoreName}". Showing all sales instead.`;
            storesSalesByStore.value = storesSales.value;
        } else {
            error_details.value = '';
        }
    } catch (error: any) {
        console.error('Error fetching stores sales:', error);
        error_details.value = String(error.message || error);
    }
});

onMounted(async () => {
    try {
        const response = await storesServices.getStores();
        dataStore.value = response.data.data.map((store: { id: number; name: string }) => ({ 
            storeId: store.id, 
            storeName: store.name 
        }));

        // Solo asigna a selectedOption, selectedStoreId se calcula automáticamente
        if (dataStore.value.length > 0) {
            const savedOption = Number(localStorage.getItem('storesSalesOption'));
            if (savedOption && dataStore.value.some((s: any) => s.storeId === savedOption)) {
                selectedOption.value = savedOption;
            } else {
                selectedOption.value = dataStore.value[0]!.storeId;
            }
        }

        

    } catch (error) {
        error_data.value = true;
    }

    }
);

function downloadPdf() {
    if (selectedOption.value) {
        window.open(`http://localhost:8000/api/reports/sales/store/${selectedOption.value}`, '_blank');
    }
}
</script>

<template>
    <ErrorMessage v-if="error_data"
        tittle="Error al cargar las ventas por sucursal"
        message="Hubo un error al obtener los datos. Por favor, inténtalo de nuevo más tarde o contacta al soporte si el problema persiste."
    />
    <div v-else class="data-view">
        <div class="toolbar">
            <h1>Ventas por Sucursal</h1>
            <div class="toolbar-actions">
                <router-link to="/data/form-sales" class="btn-new">+ Agregar Venta</router-link>
            </div>
        </div>
        <div v-if="error_details" style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid red; border-radius: 4px;">
            Atención: {{ error_details }}
        </div>
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

        <section class="button-section">
            <button class="action-button pdf-button" @click="downloadPdf">Exportar PDF</button>
            <button class="action-button excel-button">Exportar Excel</button>
        </section>
        
        <div class="table-container">
            <section class="table-section">
                <thead class="table-header">
                    <tr class="table-row">
                        <th class="table-cell">Departamento</th>
                        <th class="table-cell">Cantidad Vendida</th>
                        <th class="table-cell">Precio Unitario</th>
                        <th class="table-cell">Subtotal</th>
                        <th class="table-cell">Metodo de Pago</th>
                        <th class="table-cell">Fecha de la Venta</th>
                        <th class="table-cell">Tipo de Venta</th>
                        <th class="table-cell">Usuario</th>
                        <th class="table-cell">Total de Ventas</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <tr v-for="(sale, index) in storesSalesByStore" :key="index" class="table-row">
                        <td class="table-cell">{{ sale.department }}</td>
                        <td class="table-cell">{{ sale.quantity }}</td>
                        <td class="table-cell">$ {{ sale.unit_price }}</td>
                        <td class="table-cell">$ {{ sale.subtotal }}</td>
                        <td class="table-cell">
                            <span v-if="!sale.payment || (Array.isArray(sale.payment) && sale.payment.length === 0)">
                            </span>
                            <span v-else-if="Array.isArray(sale.payment)">
                                <div v-for="pay in sale.payment" :key="pay.id_payment">
                                    {{ pay.payment }}
                                </div>
                            </span>
                            <span v-else-if="typeof sale.payment === 'object'">
                                {{ sale.payment.payment || 'Sin especificar' }}
                            </span>
                            <span v-else>
                                {{ sale.payment }}
                            </span>
                        </td>
                        <td class="table-cell">{{ sale.transaction_date }}</td>
                        <td class="table-cell">{{ sale.transaction_type }}</td>
                        <td class="table-cell">{{ sale.user_name }}</td>
                        <td class="table-cell">$ {{ sale.total_amount }}</td>
                    </tr>
                </tbody>
            </section>
        </div>
    </div>
</template>

<style src="@/assets/styles/data/components/storeSales.css" scoped>
</style>