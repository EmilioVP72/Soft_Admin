<script setup lang="ts">

import { onMounted, ref } from 'vue';
import MovesServices from '@/services/MovesServices';

const inputs = ref([]);

onMounted(async () => {
    try {
        var response = await MovesServices.getAllInputs();
        console.log(response.data.data);
        inputs.value = response.data.data.map((input: any) => {
            return {
                id_transaction: input.id_transaction,
                store: input.store?.store,
                user: input.user?.name,
                payment: input.payment?.payment,
                total_amount: input.total_amount,
                transaction_type: input.transaction_type,
                notes: input.notes,
                transaction_date: input.transaction_date
            };
        });
    } catch (error) {
        console.log(error);
    }
});


</script>

<template>
    <section class="data-view inputs">
        <div class="toolbar">
            <h1>Entradas</h1>
            <div class="toolbar-actions">
                <section class="filters-search">
                    <input type="text" class="form-control" placeholder="Buscar">
                    <button class="btn-print">Buscar</button>
                    <button class="btn-print">Limpiar</button>
                </section>
                <section class="filters-export">
                    <button class="btn-print">Exportar PDF</button>
                    <button class="btn-print">Exportar Excel</button>
                </section>
                <section class="filters-select">
                    <label for="" style="color:white; margin-right: 0.5rem; font-size: 0.9rem;">Filtrar</label>
                    <select class="form-control">
                        <option value="">Todos</option>
                    </select>
                </section>
                <button class="btn-new">Agregar</button>
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
                    <tr class="data-row" v-for="input in inputs" :key="input.id_transaction">
                        <td class="data-cell">{{ input.id_transaction }}</td>
                        <td class="data-cell">{{ input.store }}</td>
                        <td class="data-cell">{{ input.user }}</td>
                        <td class="data-cell">{{ input.payment }}</td>
                        <td class="data-cell">{{ input.total_amount }}</td>
                        <td class="data-cell">{{ input.transaction_type }}</td>
                        <td class="data-cell">{{ input.notes }}</td>
                        <td class="data-cell">{{ input.transaction_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
@import '@/assets/styles/moves/inputs.css';
</style>