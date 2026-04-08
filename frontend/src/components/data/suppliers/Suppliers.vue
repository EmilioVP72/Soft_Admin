<script setup lang="ts">
import { onMounted, ref } from 'vue';
import SuppliersServices from '@/services/SuppliersServices';
import SuppliersForm from './SuppliersForm.vue';
import SupplierPaymentForm from './SupplierPaymentForm.vue';
import ConfirmModal from '@/components/shared/ConfirmModal.vue';
import ErrorComponent from '@/components/shared/Error.vue';
import { normalizeSearchText } from '@/utils/search';

const suppliers = ref<any[]>([]);
const originalSuppliers = ref<any[]>([]);
const showModal = ref(false);
const showPaymentModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedSupplier = ref<any>(null);

const hasError = ref(false);
const errorData = ref({ tittle: '', message: '' });

const searchQuery = ref('');
const filterSupplier = ref('');
const filterPaymentsMin = ref('');
const filterPaymentsMax = ref('');
const filterTotalPaidMin = ref('');
const filterTotalPaidMax = ref('');
const supplierOptions = ref<string[]>([]);

const loadData = async () => {
    try {
        var response = await SuppliersServices.getAllSuppliers();
        
        // Mapeamos los proveedores y al mismo tiempo consultamos los pagos de cada uno
        const suppliersData = response.data.data;
        
        const mappedSuppliers = await Promise.all(suppliersData.map(async (supplier: any) => {
            // Obtenemos los pagos específicos de este proveedor usando su ID en lugar de un contador `i`
            var responsePayments = await SuppliersServices.getAllPaymentsToSuppliers(supplier.id_supplier);
            const payments = [...(responsePayments.data.data || [])].sort((a: any, b: any) =>
                Number(a.id_payment_supplier || a.id_payment || a.id || 0) - Number(b.id_payment_supplier || b.id_payment || b.id || 0)
            );
            
            // Calculamos el total pagado sumando la propiedad "amount_paid"
            const totalPaid = payments.reduce((acc: number, payment: any) => acc + parseFloat(payment.amount_paid), 0);

            return {
                id: supplier.id_supplier,
                name: supplier.supplier,
                payments: payments,
                totalPaid: totalPaid
            }
        }));

        originalSuppliers.value = [...mappedSuppliers].sort((a: any, b: any) => Number(a.id || 0) - Number(b.id || 0));
        supplierOptions.value = Array.from(new Set(mappedSuppliers.map((supplier: any) => supplier.name).filter(Boolean)));
        applyFilters();

    } catch (error) {
        hasError.value = true;
        errorData.value = { tittle: 'Error de Conexión', message: 'No se pudieron cargar los proveedores.' };
    }
};

onMounted(() => {
    loadData();
});

const handleSupplierSaved = () => {
    showModal.value = false;
    loadData(); // reload table wrapper
};

const openAddModal = () => {
    selectedSupplier.value = null;
    showModal.value = true;
};

const openEditModal = (supplier: any) => {
    selectedSupplier.value = supplier;
    showModal.value = true;
};

const openDeleteConfirm = (supplier: any) => {
    selectedSupplier.value = supplier;
    showDeleteConfirm.value = true;
};

const handleDelete = async () => {
    if (!selectedSupplier.value) return;
    try {
        await SuppliersServices.deleteSupplier(selectedSupplier.value.id);
        showDeleteConfirm.value = false;
        loadData();
    } catch (error) {
        hasError.value = true;
        errorData.value = { tittle: 'Error al Eliminar', message: 'No se pudo eliminar el proveedor.' };
        showDeleteConfirm.value = false;
    }
};

const openPaymentModal = (supplier: any) => {
    selectedSupplier.value = supplier;
    showPaymentModal.value = true;
};

const handlePaymentSaved = () => {
    showPaymentModal.value = false;
    loadData(); // actualiza la tabla con los nuevos totales
};

const applyFilters = () => {
    let result = [...originalSuppliers.value];

    if (filterSupplier.value) {
        result = result.filter((supplier: any) => supplier.name === filterSupplier.value);
    }

    if (filterPaymentsMin.value !== '' || filterPaymentsMax.value !== '') {
        result = result.filter((supplier: any) => {
            const totalPayments = Number(supplier.payments?.length || 0);
            const min = filterPaymentsMin.value !== '' ? Number(filterPaymentsMin.value) : -Infinity;
            const max = filterPaymentsMax.value !== '' ? Number(filterPaymentsMax.value) : Infinity;
            return totalPayments >= min && totalPayments <= max;
        });
    }

    if (filterTotalPaidMin.value !== '' || filterTotalPaidMax.value !== '') {
        result = result.filter((supplier: any) => {
            const totalPaid = Number(supplier.totalPaid) || 0;
            const min = filterTotalPaidMin.value !== '' ? Number(filterTotalPaidMin.value) : -Infinity;
            const max = filterTotalPaidMax.value !== '' ? Number(filterTotalPaidMax.value) : Infinity;
            return totalPaid >= min && totalPaid <= max;
        });
    }

    const query = normalizeSearchText(searchQuery.value);
    if (query !== '') {
        result = result.filter((supplier: any) => {
            const searchableValues = [
                supplier.id,
                supplier.name,
                supplier.payments?.length,
                supplier.totalPaid,
            ];

            return searchableValues.some((value: any) => {
                if (value === null || value === undefined) return false;
                return normalizeSearchText(value).includes(query);
            });
        });
    }

    suppliers.value = result;
};

const searchSuppliers = () => {
    applyFilters();
};

const clearFilters = () => {
    searchQuery.value = '';
    filterSupplier.value = '';
    filterPaymentsMin.value = '';
    filterPaymentsMax.value = '';
    filterTotalPaidMin.value = '';
    filterTotalPaidMax.value = '';
    applyFilters();
};

</script>

<template>
    <div class="container">
        <ErrorComponent v-if="hasError" :tittle="errorData.tittle" :message="errorData.message" />
        <template v-else>
            <section class="header-container">
                <h1>Proveedores</h1>
                <div class="header-buttons">
                    <section class="filters-search">
                        <input v-model="searchQuery" @keyup.enter="searchSuppliers" type="text" class="form-control" placeholder="Buscar">
                        <button class="btn-pdf" @click="searchSuppliers">Buscar</button>
                        <button class="btn-excel" @click="clearFilters">Limpiar</button>
                    </section>

                    <section class="filters-export">
                        <button class="btn-pdf">Exportar a PDF</button>
                        <button class="btn-excel">Exportar a Excel</button>
                    </section>

                    <section class="filters-select" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Proveedor</label>
                            <select class="form-control" v-model="filterSupplier" @change="applyFilters">
                                <option value="">Todos</option>
                                <option v-for="supplier in supplierOptions" :key="supplier" :value="supplier">{{ supplier }}</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">No. Pagos (Mín - Máx)</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="number" class="form-control" style="width: 80px;" placeholder="Min" v-model="filterPaymentsMin" @input="applyFilters">
                                <input type="number" class="form-control" style="width: 80px;" placeholder="Máx" v-model="filterPaymentsMax" @input="applyFilters">
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column;">
                            <label style="color:white; font-size: 0.8rem;">Total Pagado (Mín - Máx)</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="number" step="0.01" class="form-control" style="width: 90px;" placeholder="Min" v-model="filterTotalPaidMin" @input="applyFilters">
                                <input type="number" step="0.01" class="form-control" style="width: 90px;" placeholder="Máx" v-model="filterTotalPaidMax" @input="applyFilters">
                            </div>
                        </div>
                    </section>

                    <button class="btn-new" @click="openAddModal">Agregar Proveedor</button>
                </div>
            </section>
            <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Proveedor</th>
                        <th>No. Pagos Registrados</th>
                        <th>Total Pagado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="supplier in suppliers" :key="supplier.id">
                        <td>{{ supplier.id }}</td>
                        <td>{{ supplier.name }}</td>
                        <td>{{ supplier.payments.length }}</td>
                        <td>${{ supplier.totalPaid.toFixed(2) }}</td>
                        <td>
                            <button class="btn-pay" @click="openPaymentModal(supplier)">Agregar Pago</button>
                            <button class="btn-edit" @click="openEditModal(supplier)">Editar</button>
                            <button class="btn-delete" @click="openDeleteConfirm(supplier)">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
        </template>

        <!-- Modal Agregar/Editar Proveedor -->
        <SuppliersForm 
            v-if="showModal" 
            :supplier="selectedSupplier"
            @close="showModal = false" 
            @saved="handleSupplierSaved" 
        />
        
        <!-- Modal Agregar Pago -->
        <SupplierPaymentForm 
            v-if="showPaymentModal" 
            :supplier="selectedSupplier" 
            @close="showPaymentModal = false" 
            @saved="handlePaymentSaved" 
        />

        <!-- Alerta Eliminar -->
        <ConfirmModal
            v-if="showDeleteConfirm"
            title="Eliminar proveedor"
            :message="`¿Estás seguro que deseas eliminar permanentemente el proveedor '${selectedSupplier?.name}' junto con sus pagos?`"
            confirmLabel="Sí, eliminar"
            cancelLabel="Cancelar"
            :danger="true"
            @confirm="handleDelete"
            @cancel="showDeleteConfirm = false"
        />
    </div>
</template>

<style scoped>
@import '@/assets/styles/data/components/suppliers.css';
</style>