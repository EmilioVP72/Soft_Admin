<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import storesServices from '@/services/StoresServices';
import ErrorMessage from '@/components/shared/Error.vue';
import { useNotification } from '@/composables/useNotification';
import { normalizeSearchText } from '@/utils/search';
import { formatDateTime24, toDateInputValue } from '@/utils/datetime';
import ReportsServices from '@/services/ReportsServices';

const error_data = ref<boolean>(false);
const error_details = ref<string>('');
const storesSales = ref<any[]>([]);
const { showError } = useNotification();
const storesSalesByStore = ref<any[]>([]);
const originalStoresSalesByStore = ref<any[]>([]);
const dataStore = ref<Array<{ storeId: number; storeName: string }>>([]);
var selectedOption = ref<number>(Number(localStorage.getItem('storesSalesOption')) || 0);

const searchQuery = ref('');
const filterDepartment = ref('');
const filterPayment = ref('');
const filterTotalMin = ref('');
const filterTotalMax = ref('');
const filterDate = ref('');
const departmentOptions = ref<string[]>([]);
const paymentOptions = ref<string[]>([]);

// selectedStoreId es computed, se calcula automáticamente cuando selectedOption cambia
const selectedStoreId = computed(() => {
    return selectedOption.value;
});

const loadSalesForSelectedStore = async () => {
    if (!selectedOption.value) {
        storesSales.value = [];
        storesSalesByStore.value = [];
        originalStoresSalesByStore.value = [];
        paymentOptions.value = [];
        departmentOptions.value = [];
        return;
    }

    try {
        const saleData = await storesServices.getSalesbyDepartmentForStore();
        
        let flattenedSales: any[] = [];
        (saleData.data.data || []).forEach((sale: any) => {
            const baseSale = {
                notes: sale.notes,
                store_name: sale.store_name || (sale.store && sale.store.name) || 'N/A',
                total_amount: sale.total_amount,
                transaction_date_raw: sale.transaction_date,
                transaction_date: formatDateTime24(sale.transaction_date),
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
        
        storesSales.value = [...flattenedSales].sort((a: any, b: any) =>
            Number(a.id_transaction_detail ?? Number.MAX_SAFE_INTEGER) - Number(b.id_transaction_detail ?? Number.MAX_SAFE_INTEGER)
        );

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

        originalStoresSalesByStore.value = [...storesSalesByStore.value];
        departmentOptions.value = Array.from(new Set(
            originalStoresSalesByStore.value
                .map((sale: any) => sale.department)
                .filter((dep: any) => dep !== null && dep !== undefined && String(dep).trim() !== '')
        )).sort((a: any, b: any) => String(a).localeCompare(String(b), 'es', { sensitivity: 'base' }));
        paymentOptions.value = Array.from(new Set(
            originalStoresSalesByStore.value
                .flatMap((sale: any) => {
                    if (!sale.payment) return [];
                    if (Array.isArray(sale.payment)) {
                        return sale.payment.map((pay: any) => pay?.payment).filter(Boolean);
                    }
                    if (typeof sale.payment === 'object') {
                        return [sale.payment.payment].filter(Boolean);
                    }
                    return [sale.payment].filter(Boolean);
                })
        ));
        applyFilters();
    } catch (error: any) {
        showError('Error', 'No se pudieron cargar los datos de ventas por sucursal.');
        error_details.value = String(error.message || error);
    }
};

// Watch para ver los cambios cuando seleccionas otra sucursal
watch(selectedOption, async (_newValue) => {
    localStorage.setItem('storesSalesOption', String(_newValue));
    await loadSalesForSelectedStore();
});

onMounted(async () => {
    try {
        const response = await storesServices.getStores();
        dataStore.value = response.data.data.map((store: { id: number; name: string }) => ({ 
            storeId: store.id, 
            storeName: store.name 
        })).sort((a: any, b: any) => Number(a.storeId || 0) - Number(b.storeId || 0));

        // Solo asigna a selectedOption, selectedStoreId se calcula automáticamente
        if (dataStore.value.length > 0) {
            const savedOption = Number(localStorage.getItem('storesSalesOption'));
            if (savedOption && dataStore.value.some((s: any) => s.storeId === savedOption)) {
                selectedOption.value = savedOption;
            } else {
                selectedOption.value = dataStore.value[0]!.storeId;
            }

            await loadSalesForSelectedStore();
        }

        

    } catch (error) {
        error_data.value = true;
    }

    }
);

function downloadPdf() {
    if (selectedOption.value) {
        ReportsServices.openStoreSalesPdf(selectedOption.value);
    }
}

function downloadExcel() {
    if (selectedOption.value) {
        ReportsServices.openStoreSalesExcel(selectedOption.value);
    }
}

function paymentText(payment: any): string {
    if (!payment) return '';
    if (Array.isArray(payment)) {
        return payment.map((pay: any) => pay?.payment).filter(Boolean).join(' | ');
    }
    if (typeof payment === 'object') {
        return payment.payment || '';
    }
    return String(payment);
}

function applyFilters() {
    let result = [...originalStoresSalesByStore.value];

    if (filterDepartment.value) {
        result = result.filter((sale: any) => String(sale.department || '') === filterDepartment.value);
    }

    if (filterPayment.value) {
        result = result.filter((sale: any) => paymentText(sale.payment).includes(filterPayment.value));
    }

    if (filterTotalMin.value !== '' || filterTotalMax.value !== '') {
        result = result.filter((sale: any) => {
            const total = Number(sale.total_amount) || 0;
            const min = filterTotalMin.value !== '' ? Number(filterTotalMin.value) : -Infinity;
            const max = filterTotalMax.value !== '' ? Number(filterTotalMax.value) : Infinity;
            return total >= min && total <= max;
        });
    }

    if (filterDate.value) {
        result = result.filter((sale: any) => {
            const normalizedDate = toDateInputValue(sale.transaction_date_raw || sale.transaction_date);
            if (!normalizedDate) return false;
            return normalizedDate === filterDate.value;
        });
    }

    const query = normalizeSearchText(searchQuery.value);
    if (query !== '') {
        result = result.filter((sale: any) => {
            const searchableValues = [
                sale.department,
                sale.quantity,
                sale.unit_price,
                sale.subtotal,
                sale.transaction_date,
                sale.user_name,
                sale.total_amount,
                sale.store_name,
            ];

            return searchableValues.some((value: any) => {
                if (value === null || value === undefined) return false;
                return normalizeSearchText(value).includes(query);
            });
        });
    }

    storesSalesByStore.value = result;
}

function searchSales() {
    applyFilters();
}

function clearFilters() {
    searchQuery.value = '';
    filterDepartment.value = '';
    filterPayment.value = '';
    filterTotalMin.value = '';
    filterTotalMax.value = '';
    filterDate.value = '';
    applyFilters();
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
                <div class="toolbar-groups">
                    <section class="filters-search">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="searchSales"
                            type="text"
                            class="form-control"
                            placeholder="Buscar"
                        >
                        <button class="btn-print" @click="searchSales">Buscar</button>
                        <button class="btn-print" @click="clearFilters">Limpiar</button>
                    </section>

                    <section class="filters-export">
                        <button class="btn-print" @click="downloadPdf">Exportar PDF</button>
                        <button class="btn-print" @click="downloadExcel">Exportar Excel</button>
                    </section>

                    <section class="filters-select" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; flex-direction: column;">
                            <label class="component-label" style="color:white; font-size: 0.8rem; text-align: left;">Sucursal</label>
                            <select class="form-control" v-model="selectedOption">
                                <option v-for="option in dataStore" :key="option.storeId" :value="option.storeId">
                                    {{ option.storeName }}
                                </option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Departamento</label>
                            <select class="form-control" v-model="filterDepartment" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="department in departmentOptions" :key="department" :value="department">{{ department }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Método de Pago</label>
                            <select class="form-control" v-model="filterPayment" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="payment in paymentOptions" :key="payment" :value="payment">{{ payment }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Total (Mín - Máx)</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="number" class="form-control" placeholder="Min" style="width: 80px;" v-model="filterTotalMin" @input="applyFilters">
                                <input type="number" class="form-control" placeholder="Máx" style="width: 80px;" v-model="filterTotalMax" @input="applyFilters">
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Fecha</label>
                            <input type="date" class="form-control" v-model="filterDate" @change="applyFilters">
                        </div>
                    </section>
                </div>

                <div class="toolbar-primary-action">
                    <router-link to="/data/form-sales" class="btn-new">+ Agregar Venta</router-link>
                </div>
            </div>
        </div>
        <div v-if="error_details" style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid red; border-radius: 4px;">
            Atención: {{ error_details }}
        </div>
        <div class="table-container">
            <section class="table-section">
                <thead class="table-header">
                    <tr class="table-row">
                        <th class="table-cell">Departamento</th>
                        <th class="table-cell">Cantidad Vendida</th>
                        <th class="table-cell">Precio Unitario</th>
                        <th class="table-cell">Subtotal</th>
                        <th class="table-cell">Fecha de la Venta</th>
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
                        <td class="table-cell">{{ sale.transaction_date }}</td>
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