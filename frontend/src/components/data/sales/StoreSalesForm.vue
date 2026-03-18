<script setup lang="ts">
import { onMounted, ref } from 'vue';
import storesServices from '@/services/StoresServices';
import LoginServices from '@/services/LoginServices';

const stores = ref<any>([]);
const user = ref<string>('');

onMounted(async () => {
    try {
        const responseStores = await storesServices.getStores();
        stores.value = responseStores.data.data;
        const response = await storesServices.getOneStore(stores.value[0].id);
        console.log(response.data.data)
        console.log(stores.value)
        const responseUser = await LoginServices.meUser();
        user.value = responseUser.data.data.user;
        

    } catch (error) {

    }
    
});

</script>

<template>

<div class="sales-form-container">
    <h1>Agregar Ventas por Sucursal</h1>

    <form>
        <label for="store">Sucursal:</label>
        <select name="store" id="store">
            <option value="">Seleccionar Sucursal</option>
            <option v-for="store in stores" :key="store.id" :value="store.id" placeholder="Seleccionar Sucursal">
                {{ store.name }}
            </option>
        </select>
        <label for="user">Usuario:</label>
        <input type="text" :value="user" readonly>
        <label for="payment">Metodo de Pago:</label>
        <select name="payment" id="payment">
            <option value="">Seleccionar Metodo de Pago</option>
            <option v-for="payment in payments" :key="payment.id" :value="payment.id">
                {{ payment.name }}
            </option>
        </select>
        <input type="number" placeholder="Total de la Venta">
        <input type="text" placeholder="Notas">

        <section class="details">

            <section>
                <h3>Ventas Realizadas</h3>
                <button>Agregar Venta</button>
                <select name="department" id="department">
                    <option value="">Seleccionar Departamento</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">
                        {{ department.name }}
                    </option>
                </select>
                <input type="number" placeholder="Cantidad">
                <input type="number" placeholder="Precio Unitario">
                <input type="number" placeholder="Subtotal">
            </section>

            <section>
                <h3>Resumen</h3>
                <ul>
                    <li v-for="(detail, index) in salesDetails" :key="index">
                        
                        {{ detail.quantity }}x {{ detail.department }} - 
                        ${{ detail.unit_price }} c/u 
                        (Subtotal: ${{ detail.subtotal }})
                        
                    </li>
                </ul>
            </section>

        </section>

    </form>
</div>    
    
</template>

<style scoped>
@import '@/assets/styles/data/components/storesSalesForm.css';
</style>
