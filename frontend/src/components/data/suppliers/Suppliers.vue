<script setup lang="ts">
import { onMounted, ref } from 'vue';
import SuppliersServices from '@/services/SuppliersServices';
import SuppliersForm from './SuppliersForm.vue';
import SupplierPaymentForm from './SupplierPaymentForm.vue';
import ConfirmModal from '@/components/shared/ConfirmModal.vue';

const suppliers = ref<any[]>([]);
const showModal = ref(false);
const showPaymentModal = ref(false);
const showDeleteConfirm = ref(false);
const selectedSupplier = ref<any>(null);

const loadData = async () => {
    try {
        var response = await SuppliersServices.getAllSuppliers();
        
        // Mapeamos los proveedores y al mismo tiempo consultamos los pagos de cada uno
        const suppliersData = response.data.data;
        
        suppliers.value = await Promise.all(suppliersData.map(async (supplier: any) => {
            // Obtenemos los pagos específicos de este proveedor usando su ID en lugar de un contador `i`
            var responsePayments = await SuppliersServices.getAllPaymentsToSuppliers(supplier.id_supplier);
            const payments = responsePayments.data.data;
            
            // Calculamos el total pagado sumando la propiedad "amount_paid"
            const totalPaid = payments.reduce((acc: number, payment: any) => acc + parseFloat(payment.amount_paid), 0);

            return {
                id: supplier.id_supplier,
                name: supplier.supplier,
                payments: payments,
                totalPaid: totalPaid
            }
        }));

    } catch (error) {
        console.log(error);
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
        console.error("Error eliminando el proveedor:", error);
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

</script>

<template>
    <div class="container">
        <section class="header-container">
            <h1>Proveedores</h1>
            <div class="header-buttons">
                <button class="btn-new" @click="openAddModal">Agregar Proveedor</button>
                <button class="btn-pdf">Exportar a PDF</button>
                <button class="btn-excel">Exportar a Excel</button>
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