<script setup lang="ts">

import { onMounted, ref } from 'vue';
import MovesServices from '@/services/MovesServices';

const outputs = ref([]);

onMounted(async () => {
    try {
        var response = await MovesServices.getAllOutputs();
        console.log(response.data.data);
        outputs.value = response.data.data.map((output: any) => {
            return {
                id_transaction: output.id_transaction,
                store: output.store?.store,
                user: output.user?.name,
                payment: output.payment?.payment,
                total_amount: output.total_amount,
                transaction_type: output.transaction_type,
                notes: output.notes,
                transaction_date: output.transaction_date
            };
        });
    } catch (error) {
        console.log(error);
    }
});


</script>

<template>
    <section class="data-view outputs">
        <div class="toolbar">
            <h1>Salidas</h1>
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
        <div class="outputs-body table-section">
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
                    <tr class="data-row" v-for="output in outputs" :key="output.id_transaction">
                        <td class="data-cell">{{ output.id_transaction }}</td>
                        <td class="data-cell">{{ output.store }}</td>
                        <td class="data-cell">{{ output.user }}</td>
                        <td class="data-cell">{{ output.payment }}</td>
                        <td class="data-cell">{{ output.total_amount }}</td>
                        <td class="data-cell">{{ output.transaction_type }}</td>
                        <td class="data-cell">{{ output.notes }}</td>
                        <td class="data-cell">{{ output.transaction_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<style scoped>
@import '@/assets/styles/moves/outputs.css';
</style>