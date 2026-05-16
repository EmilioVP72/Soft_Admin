<script setup lang="ts">
// --- IMPORTACIONES ---
import { onMounted, ref, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import storesServices from '@/services/StoresServices';
import LoginServices from '@/services/LoginServices';
import paymentServices from '@/services/PaymentServices';
import departmentsServices from '@/services/DepartmentsServices';
import { useNotification } from '@/composables/useNotification';

// --- ESTADO GENERAL Y VARIABLES REACTIVAS ---
const stores = ref<any>([]);
const user = ref<string>('');
const userId = ref<number>(0);
const payments = ref<any>([]);
var selectedStore = ref<number>(0);
var selectedDepartment = ref<number | ''>('');
var quantity = ref<number>(0);
var unitPrice = ref<number>(0);

const selectedPayment = ref<number | string>('');
const totalSale = ref<number>(0);
const notes = ref<string>('');
const { showWarning, showError, showSuccess, handleApiError } = useNotification();
const router = useRouter();

// --- PROPIEDADES COMPUTADAS ---
// Obtiene la opción seleccionada de sucursal
const selectStoreOption = computed(() => {
    return selectedStore.value;
});

// AQUÍ SE CALCULA EL TOTAL ACUMULADO (Normal, sin comisiones)
// Suma los subtotales de todas las partidas agregadas en la tabla de "Resumen de Ventas"
const summaryTotal = computed(() => {
    return salesDetails.value.reduce((acc: number, detail: any) => acc + (Number(detail.subtotal) || 0), 0);
});

// Verifica dinámicamente si el método de pago seleccionado es tarjeta
const isCardPayment = computed(() => {
    const payment = payments.value.find((p: any) => (p.id_payment || p.id) === selectedPayment.value);
    if (!payment) return false;
    return String(payment.payment).toLowerCase().includes('tarjeta');
});

// Calcula el total final real (aplicando la comisión/descuento de tarjeta si aplica)
const finalAmount = computed(() => {
    const total = Number(totalSale.value) || 0;
    if (isCardPayment.value) {
        return total * 0.97; // 3% menos de comisión
    }
    return total;
});

// Calcula el subtotal actual del formulario de nueva partida (Cantidad * Precio Unitario)
const currentSubtotal = computed(() => {
    return (Number(quantity.value) || 0) * (Number(unitPrice.value) || 0);
});

const departments = ref<any>([]);
const salesDetails = ref<any>([]);

const sanitizeText = (value: string) => String(value || '').trim();

// --- FUNCIONES DE VALIDACIÓN ---
// Valida los datos de una partida individual antes de agregarla a la tabla
const validateDetail = () => {
    if (!selectedStore.value) {
        showWarning('Sucursal requerida', 'Debes seleccionar una sucursal antes de agregar ventas.');
        return false;
    }

    if (!selectedDepartment.value) {
        showWarning('Departamento requerido', 'Selecciona un departamento para la venta.');
        return false;
    }

    const qty = Number(quantity.value) || 0;
    if (qty <= 0 || !Number.isFinite(qty)) {
        showWarning('Cantidad inválida', 'La cantidad debe ser mayor a 0.');
        return false;
    }

    const price = Number(unitPrice.value) || 0;
    if (price <= 0 || !Number.isFinite(price)) {
        showWarning('Precio inválido', 'El precio unitario debe ser mayor a 0.');
        return false;
    }

    if (currentSubtotal.value <= 0) {
        showWarning('Subtotal inválido', 'El subtotal de la partida debe ser mayor a 0.');
        return false;
    }

    return true;
};

// Valida toda la transacción general antes de enviarla a la base de datos
const validateTransaction = () => {
    if (!selectedStore.value) {
        showWarning('Sucursal requerida', 'Debes seleccionar una sucursal.');
        return false;
    }

    if (!selectedPayment.value) {
        showWarning('Método de pago requerido', 'Debes seleccionar un método de pago.');
        return false;
    }

    if (salesDetails.value.length === 0) {
        showWarning('Sin partidas', 'Debes agregar al menos una venta al resumen.');
        return false;
    }

    const hasInvalidDetail = salesDetails.value.some((detail: any) => {
        const qty = Number(detail.quantity) || 0;
        const price = Number(detail.unit_price) || 0;
        const subtotal = Number(detail.subtotal) || 0;
        return !detail.fk2_id_department || qty <= 0 || price <= 0 || subtotal <= 0;
    });

    if (hasInvalidDetail) {
        showWarning('Detalle inválido', 'Hay partidas con datos inválidos. Verifica departamento, cantidad, precio y subtotal.');
        return false;
    }

    const total = Number(totalSale.value) || 0;
    if (total <= 0) {
        showWarning('Total inválido', 'El total de la venta debe ser mayor a 0.');
        return false;
    }

    const notesLength = sanitizeText(notes.value).length;
    if (notesLength > 500) {
        showWarning('Notas demasiado largas', 'Las notas no deben exceder 500 caracteres.');
        return false;
    }

    const diff = Math.abs(total - Number(summaryTotal.value || 0));
    if (diff > 0.01) {
        showWarning('Total Incorrecto', 'El Total de la Venta no coincide con el Total Acumulado en el resumen.');
        return false;
    }

    return true;
};

// --- OBSERVADORES (WATCHERS) Y CICLO DE VIDA (ONMOUNTED) ---
// Observa cambios en la sucursal seleccionada para cargar sus departamentos correspondientes
watch(selectStoreOption, async (newValue: number | '') => {
    const storeId = Number(newValue);
    if (!storeId) return;
    const response = await departmentsServices.getDepartmentsByStore(storeId);
    departments.value = response.data.data.map((department: any) => ({
        department: department.department,
        id: department.id_department
    })).sort((a: any, b: any) => Number(a.id || 0) - Number(b.id || 0));
});

// Carga los datos iniciales al montar el componente (sucursales, usuario actual y métodos de pago)
onMounted(async () => {
    try {
        const responseStores = await storesServices.getStores();
        stores.value = [...(responseStores.data.data || [])].sort((a: any, b: any) =>
            Number(a.id_store || a.id || 0) - Number(b.id_store || b.id || 0)
        );
        const responseUser = await LoginServices.meUser();
        user.value = responseUser.data.data.user;
        userId.value = responseUser.data.data.id_user || responseUser.data.data.id || responseUser.data.data.id_personal_info || 1;
        const responsePayments = await paymentServices.getPayments();
        payments.value = [...(responsePayments.data.data || [])].sort((a: any, b: any) =>
            Number(a.id_payment || a.id || 0) - Number(b.id_payment || b.id || 0)
        );

       
        
    } catch (error) {
        handleApiError(error);
    }
    
});

// --- FUNCIONES PARA MANEJO DE LAS PARTIDAS (CARRITO) ---
// Agrega una nueva partida a la tabla de resumen de ventas
const addSale = () => {
    if (!validateDetail()) return;

    const dept = departments.value.find((d: any) => d.id === selectedDepartment.value);
    
    salesDetails.value.push({
        fk2_id_department: selectedDepartment.value,
        department: dept ? dept.department : '',
        quantity: quantity.value,
        unit_price: unitPrice.value,
        subtotal: currentSubtotal.value,
    });

    selectedDepartment.value = 0;
    quantity.value = 0;
    unitPrice.value = 0;
}

// Elimina una partida de la tabla según su índice
const removeSale = (index: number | string) => {
    salesDetails.value.splice(Number(index), 1);
}

const goBack = () => {
    if (window.history.length > 1) {
        router.back();
        return;
    }
    router.push('/data');
};

// --- FUNCIÓN PRINCIPAL DE GUARDADO ---
// Construye el payload y envía la petición POST al backend para registrar la transacción
const saveTransaction = async () => {
    if (!validateTransaction()) return;

    const transactionData = {
        fk1_id_store: selectedStore.value,
        fk2_id_user: userId.value,
        fk3_id_payment: Number(selectedPayment.value),
        total_amount: finalAmount.value, // Se envía el total con la comisión ya descontada
        notes: sanitizeText(notes.value),
        details: salesDetails.value.map((detail: any) => ({
            fk2_id_department: detail.fk2_id_department,
            quantity: detail.quantity,
            unit_price: detail.unit_price,
            subtotal: detail.subtotal
        }))
    };
    

    try {
        await storesServices.createSale({
            sales: [transactionData]
        });
        
        showSuccess('Éxito', '¡Transacción guardada exitosamente!');
        
        // Limpiamos todo al finalizar
        salesDetails.value = [];
        selectedStore.value = 0;
        selectedPayment.value = '';
        totalSale.value = 0;
        notes.value = '';
    } catch (error) {
        handleApiError(error);
    }
}



</script>

<template>

<div class="sales-form-container">
    <!-- ENCABEZADO -->
    <div class="form-title-row">
        <h1 class="form-title">Agregar Ventas por Sucursal</h1>
        <button type="button" class="btn-back" @click="goBack">Regresar</button>
    </div>

    <form @submit.prevent="saveTransaction">
        <!-- DATOS GENERALES DE LA TRANSACCIÓN -->
        <label for="store">Sucursal:</label>
        <select name="store" id="store" v-model.number="selectedStore" :disabled="salesDetails.length > 0">
            <option value="">Seleccionar Sucursal</option>
            <option v-for="store in stores" :key="store.id" :value="store.id" placeholder="Seleccionar Sucursal">
                {{ store.name }}
            </option>
        </select>
        <label for="user">Usuario:</label>
        <input type="text" :value="user" readonly>
        <label for="payment">Metodo de Pago:</label>
        <select name="payment" id="payment" v-model="selectedPayment">
            <option value="">Seleccionar Metodo de Pago</option>
            <option v-for="payment in payments" :key="payment.id_payment || payment.id" :value="payment.id_payment || payment.id">
                {{ payment.payment }}
            </option>
        </select>
        <label for="totalSale">Total de la Venta (MXN):</label>
        <input type="number" step="0.01" min="0.01" placeholder="0.00" v-model.number="totalSale" required>
        
        <p v-if="isCardPayment" style="color: #d97706; font-size: 0.9em; margin-top: -10px; margin-bottom: 15px; font-weight: 500;">
            Se registrará un total de <strong>$ {{ finalAmount.toFixed(2) }}</strong> (Total menos 3% de comisión por tarjeta).
        </p>

        <label for="notes">Notas:</label>
        <input type="text" placeholder="Notas" v-model="notes" maxlength="500">

        <section class="details">

            <!-- FORMULARIO PARA AGREGAR PARTIDAS INDIVIDUALES AL CARRITO -->
            <section>
                <h3>Ventas Realizadas</h3>
                <button @click.prevent="addSale">Agregar Venta</button>
                <label for="department">Departamento:</label>
                <select name="department" id="department" v-model.number="selectedDepartment">
                    <option value="">Seleccionar Departamento</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                        {{ department.department }}
                    </option>
                </select>
                <label for="quantity">Cantidad:</label>
                <input type="number" step="1" placeholder="Cantidad" v-model.number="quantity">
                <label for="unitPrice">Precio Unitario (MXN):</label>
                <input type="number" step="0.01" placeholder="0.00" v-model.number="unitPrice">
                <label for="subtotal">Subtotal (MXN):</label>
                <input id="subtotal" type="text" :value="`$ ${currentSubtotal.toFixed(2)}`" readonly>
            </section>

            <!-- TABLA DE RESUMEN DE VENTAS (CARRITO Y TOTAL ACUMULADO) -->
            <section class="summary-section">
                <h3>Resumen de Ventas</h3>
                <div v-if="salesDetails.length === 0">
                    <p style="color: #666; font-style: italic;">No hay ventas agregadas aún.</p>
                </div>
                <div class="table-responsive" v-else>
                    <table class="summary-table" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ccc; text-align: left;">
                                <th style="padding: 8px;">Departamento</th>
                                <th style="padding: 8px;">Cantidad</th>
                                <th style="padding: 8px;">Precio Unitario</th>
                                <th style="padding: 8px;">Subtotal</th>
                                <th style="padding: 8px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(detail, index) in salesDetails" :key="index" style="border-bottom: 1px solid #eee;">
                                <td style="padding: 8px;">{{ detail.department }}</td>
                                <td style="padding: 8px;">{{ detail.quantity }}</td>
                                <td style="padding: 8px;">$ {{ Number(detail.unit_price || 0).toFixed(2) }}</td>
                                <td style="padding: 8px;">$ {{ Number(detail.subtotal || 0).toFixed(2) }}</td>
                                <td style="padding: 8px;">
                                    <button style="background-color: #ff4d4f; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" @click.prevent="removeSale(index)">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding: 8px; text-align: right;"><strong>Total Acumulado:</strong></td>
                                <td colspan="2" style="padding: 8px;">
                                    <strong>$ {{ Number(summaryTotal || 0).toFixed(2) }}</strong>
                                    <div v-if="isCardPayment" style="color: #d97706; font-size: 0.85em; margin-top: 4px; font-weight: normal;">
                                        Total con tarjeta: <strong>$ {{ (summaryTotal * 0.97).toFixed(2) }}</strong>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

        </section>

        <!-- BOTÓN FINAL DE ENVÍO -->
        <button type="submit">Agregar Transaccion</button>

    </form>
</div>    
    
</template>

<style scoped>
@import '@/assets/styles/data/components/storesSalesForm.css';
</style>
