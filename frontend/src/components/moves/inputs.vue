<script setup lang="ts">
import { onMounted, ref } from 'vue';
import MovesServices from '@/services/MovesServices';
import StoresServices from '@/services/StoresServices';
import PaymentServices from '@/services/PaymentServices';
import InputForm from './InputForm.vue';
import ErrorComponent from '@/components/shared/Error.vue';
import { normalizeSearchText } from '@/utils/search';
import { formatDateTime24 } from '@/utils/datetime';

const searchQuery = ref('');
const dataInputs = ref([]);
const originalData = ref([]);

const filterStore = ref('');
const filterPayment = ref('');

const hasError = ref(false);
const errorData = ref({ tittle: '', message: '' });
const filterTotalMin = ref('');
const filterTotalMax = ref('');
const filterDate = ref('');

const storesOptions = ref([]);
const paymentsOptions = ref([]);

const isLoading = ref(false);
const isInputFormOpen = ref(false);

const fetchInputs = async () => {
    isLoading.value = true;
    try {
        const response = await MovesServices.getAllInputs();
        const mappedData = response.data.data.map((input: any) => {
            return {
                id_transaction: input.id_transaction,
                store: input.store?.store,
                user: input.user?.name,
                payment: input.payment?.payment,
                total_amount: input.total_amount,
                transaction_type: input.transaction_type,
                notes: input.notes,
                transaction_date: formatDateTime24(input.transaction_date)
            };
        }).sort((a: any, b: any) => a.id_transaction - b.id_transaction);

        originalData.value = mappedData;
        applyFilters();
    } catch (error) {
        hasError.value = true;
        errorData.value = {
            tittle: 'Error de Conexión',
            message: 'No se pudieron cargar las entradas. Por favor, intente de nuevo más tarde.'
        };
    } finally {
        isLoading.value = false;
    }
};

const applyFilters = () => {
    let result = [...originalData.value];

    if (filterStore.value) {
        result = result.filter((item: any) => item.store === filterStore.value);
    }

    if (filterPayment.value) {
        result = result.filter((item: any) => item.payment === filterPayment.value);
    }

    if (filterTotalMin.value !== '' || filterTotalMax.value !== '') {
        result = result.filter((item: any) => {
            const total = Number(item.total_amount) || 0;
            const min = filterTotalMin.value !== '' ? Number(filterTotalMin.value) : -Infinity;
            const max = filterTotalMax.value !== '' ? Number(filterTotalMax.value) : Infinity;
            return total >= min && total <= max;
        });
    }

    if (filterDate.value) {
        const [y, m, d] = filterDate.value.split('-');
        const formattedDate = `${d}-${m}-${y}`;
        result = result.filter((item: any) => item.transaction_date && item.transaction_date.startsWith(formattedDate));
    }

    const query = normalizeSearchText(searchQuery.value);
    if (query !== '') {
        result = result.filter((item: any) => {
            return Object.keys(item).some((key: string) => {
                const value = (item as any)[key];
                if (value === null || value === undefined) return false;
                return normalizeSearchText(value).includes(query);
            });
        });
    }

    dataInputs.value = result;
};

const searchInputs = () => {
    applyFilters();
};

const clearSearch = () => {
    searchQuery.value = '';
    filterStore.value = '';
    filterPayment.value = '';
    filterTotalMin.value = '';
    filterTotalMax.value = '';
    filterDate.value = '';
    applyFilters();
};

const handleInputSaved = () => {
    fetchInputs();
};

const fetchCatalogs = async () => {
    try {
        const [storesData, paymentsData] = await Promise.all([
            StoresServices.getStores(),
            PaymentServices.getPayments()
        ]);

        const rawStores = storesData.data.data ? storesData.data.data : storesData.data;
        const rawPayments = paymentsData.data.data ? paymentsData.data.data : paymentsData.data;

        storesOptions.value = Array.from(new Set((rawStores || []).map((s: any) => s.store || s.name).filter(Boolean)));
        paymentsOptions.value = Array.from(new Set((rawPayments || []).map((p: any) => p.payment).filter(Boolean)));
    } catch (error) {
        hasError.value = true;
        errorData.value = {
            tittle: 'Error de Catálogos',
            message: 'No se pudieron cargar los filtros. Por favor, intente de nuevo más tarde.'
        };
    }
};

onMounted(() => {
    fetchInputs();
    fetchCatalogs();
});
</script>

<template>
    <ErrorComponent v-if="hasError" :tittle="errorData.tittle" :message="errorData.message" />
    <section v-else class="data-view inputs">
        <div class="toolbar">
            <h1>Entradas</h1>
            <div class="toolbar-actions">
                <div class="toolbar-groups">
                    <section class="filters-search">
                        <input
                            v-model="searchQuery"
                            @keyup.enter="searchInputs"
                            type="text" class="form-control" placeholder="Buscar">
                        <button class="btn-print" @click="searchInputs">Buscar</button>
                        <button class="btn-print" @click="clearSearch">Limpiar</button>
                    </section>
                    <section class="filters-export">
                        <button class="btn-print">Exportar PDF</button>
                        <button class="btn-print">Exportar Excel</button>
                    </section>
                    <section class="filters-select" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; flex-direction: column;">
                            <label for="filtro-tienda" style="color:white; font-size: 0.8rem;">Tienda</label>
                            <select id="filtro-tienda" class="form-control" v-model="filterStore" @change="applyFilters">
                                <option value="">Todas</option>
                                <option v-for="store in storesOptions" :key="String(store)" :value="store">{{ store }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="filtro-pago" style="color:white; font-size: 0.8rem;">Pago</label>
                            <select id="filtro-pago" class="form-control" v-model="filterPayment" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="pay in paymentsOptions" :key="String(pay)" :value="pay">{{ pay }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="filtro-total" style="color:white; font-size: 0.8rem;">Total (Mín - Máx)</label>
                            <div style="display: flex; gap: 5px;">
                                <input id="filtro-total-min" type="number" step="0.01" class="form-control" v-model="filterTotalMin" @input="applyFilters" placeholder="Min" style="width: 80px;">
                                <input id="filtro-total-max" type="number" step="0.01" class="form-control" v-model="filterTotalMax" @input="applyFilters" placeholder="Máx" style="width: 80px;">
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label for="filtro-fecha" style="color:white; font-size: 0.8rem;">Fecha</label>
                            <input id="filtro-fecha" type="date" class="form-control" v-model="filterDate" @change="applyFilters">
                        </div>
                    </section>
                </div>
                <div class="toolbar-primary-action">
                    <button class="btn-new" @click="isInputFormOpen = true">Agregar</button>
                </div>
            </div>
        </div>
        <div class="inputs-body table-section">
            <table class="moves-table">
                <thead class="table-header">
                    <tr class="header-row">
                        <th class="header-cell">ID</th>
                        <th class="header-cell">Tienda</th>
                        <th class="header-cell">Usuario</th>
                        <th class="header-cell">Pago</th>
                        <th class="header-cell">Total</th>
                        <th class="header-cell">Tipo</th>
                        <th class="header-cell">Notas</th>
                        <th class="header-cell">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row" v-for="data in dataInputs" :key="data.id_transaction">
                        <td class="data-cell">{{ data.id_transaction }}</td>
                        <td class="data-cell">{{ data.store }}</td>
                        <td class="data-cell">{{ data.user }}</td>
                        <td class="data-cell">{{ data.payment }}</td>
                        <td class="data-cell">$ {{ data.total_amount }}</td>
                        <td class="data-cell">{{ data.transaction_type }}</td>
                        <td class="data-cell">{{ data.notes }}</td>
                        <td class="data-cell">{{ data.transaction_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <InputForm :isOpen="isInputFormOpen" @close="isInputFormOpen = false" @saved="handleInputSaved" />
    </section>
</template>

<style scoped>
@import '@/assets/styles/moves/inputs.css';
</style>
