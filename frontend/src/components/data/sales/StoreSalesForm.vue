<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue';
import storesServices from '@/services/StoresServices';
import LoginServices from '@/services/LoginServices';
import paymentServices from '@/services/PaymentServices';
import departmentsServices from '@/services/DepartmentsServices';

const stores = ref<any>([]);
const user = ref<string>('');
const userId = ref<number>(0);
const payments = ref<any>([]);
var selectedStore = ref<number>(0);
var selectedDepartment = ref<number>(0);
var quantity = ref<number>(0);
var unitPrice = ref<number>(0);
var subtotal = ref<number>(0);

const selectedPayment = ref<number | string>('');
const totalSale = ref<number>(0);
const notes = ref<string>('');

const selectStoreOption = computed(() => {
    return selectedStore.value;
});

const summaryTotal = computed(() => {
    return salesDetails.value.reduce((acc: number, detail: any) => acc + (Number(detail.subtotal) || 0), 0);
});

const departments = ref<any>([]);
const salesDetails = ref<any>([]);

watch(selectStoreOption, async (newValue: number) => {
    if (newValue === 0) return;
    const response = await departmentsServices.getDepartmentsByStore(newValue);
    departments.value = response.data.data.map((department: any) => ({
        department: department.department,
        id: department.id_department
    }));
    console.log(departments.value);
});

onMounted(async () => {
    try {
        const responseStores = await storesServices.getStores();
        stores.value = responseStores.data.data;
        const responseUser = await LoginServices.meUser();
        user.value = responseUser.data.data.user;
        userId.value = responseUser.data.data.id_user || responseUser.data.data.id || responseUser.data.data.id_personal_info || 1;
        const responsePayments = await paymentServices.getPayments();
        payments.value = responsePayments.data.data;
        console.log("MÉTODOS DE PAGO LEÍDOS:", payments.value);

       
        
    } catch (error) {

    }
    
});

const addSale = () => {
    const dept = departments.value.find((d: any) => d.id === selectedDepartment.value);
    
    salesDetails.value.push({
        fk2_id_department: selectedDepartment.value,
        department: dept ? dept.department : '',
        quantity: quantity.value,
        unit_price: unitPrice.value,
        subtotal: subtotal.value,
    });

    selectedDepartment.value = 0;
    quantity.value = 0;
    unitPrice.value = 0;
    subtotal.value = 0;
}

const removeSale = (index: number) => {
    salesDetails.value.splice(index, 1);
}

const saveTransaction = async () => {
    if (Number(totalSale.value) !== summaryTotal.value) {
        alert("El Total de la Venta no coincide con el Total Acumulado en el resumen.");
        return;
    }

    const transactionData = {
        fk1_id_store: selectedStore.value,
        fk2_id_user: userId.value,
        fk3_id_payment: selectedPayment.value,
        total_amount: totalSale.value,
        notes: notes.value,
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
        
        alert("¡Transacción guardada exitosamente!");
        
        // Limpiamos todo al finalizar
        salesDetails.value = [];
        selectedStore.value = 0;
        selectedPayment.value = '';
        totalSale.value = 0;
        notes.value = '';
    } catch (error) {
        console.error("Error al guardar en la base de datos:", error);
        alert("Hubo un error al guardar la transacción.");
    }
}



</script>

<template>

<div class="sales-form-container">
    <h1>Agregar Ventas por Sucursal</h1>

    <form @submit.prevent="saveTransaction">
        <label for="store">Sucursal:</label>
        <select name="store" id="store" v-model="selectedStore" :disabled="salesDetails.length > 0">
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
        <label for="totalSale">Total de la Venta:</label>
        <input type="number" placeholder="Total de la Venta" v-model="totalSale">
        <label for="notes">Notas:</label>
        <input type="text" placeholder="Notas" v-model="notes">

        <section class="details">

            <section>
                <h3>Ventas Realizadas</h3>
                <button @click.prevent="addSale">Agregar Venta</button>
                <label for="department">Departamento:</label>
                <select name="department" id="department" v-model="selectedDepartment">
                    <option value="">Seleccionar Departamento</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                        {{ department.department }}
                    </option>
                </select>
                <label for="quantity">Cantidad:</label>
                <input type="number" placeholder="Cantidad" v-model="quantity">
                <label for="unitPrice">Precio Unitario:</label>
                <input type="number" placeholder="Precio Unitario" v-model="unitPrice">
                <label for="subtotal">Subtotal:</label>
                <input type="number" placeholder="Subtotal" v-model="subtotal">
            </section>

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
                                <td style="padding: 8px;">${{ detail.unit_price }}</td>
                                <td style="padding: 8px;">${{ detail.subtotal }}</td>
                                <td style="padding: 8px;">
                                    <button style="background-color: #ff4d4f; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;" @click.prevent="removeSale(index)">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding: 8px; text-align: right;"><strong>Total Acumulado:</strong></td>
                                <td colspan="2" style="padding: 8px;"><strong>${{ summaryTotal }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>

        </section>

        <button type="submit">Agregar Transaccion</button>

    </form>
</div>    
    
</template>

<style scoped>
@import '@/assets/styles/data/components/storesSalesForm.css';
</style>
