<script setup lang="ts">
import { ref } from 'vue';
import Products from '@/components/inventory/products.vue'

const searchQuery = ref('');
const statusFilter = ref('all');
const productsRef = ref<any>(null);

const handleAddProduct = () => {
    if (productsRef.value) {
        productsRef.value.modalFormProduct();
    }
}
</script>

<template>
    <main class="container">
        <section class="header">
            <h1>Inventario</h1>
            <div class="header-actions">
                <button @click="handleAddProduct">Agregar Producto</button>
                
                <select v-model="statusFilter" class="filter-select">
                    <option value="all">Todos</option>
                    <option value="active">Solo Activos</option>
                    <option value="inactive">Solo Inactivos</option>
                </select>

                <input type="text" v-model="searchQuery" placeholder="Buscar nombre o código...">
            </div>
        </section>

        <Products ref="productsRef" :search="searchQuery" :status="statusFilter" />
    </main>
</template>

<style>
    @import '@/assets/styles/inventory/inventory.css';
</style>