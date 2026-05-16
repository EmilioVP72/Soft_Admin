<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import InventoryServices from '@/services/InventoryServices';
import ProductForm from './ProductForm.vue';
import { RouterLink } from 'vue-router';
import { useNotification } from '@/composables/useNotification';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';

const props = defineProps({
    search: {
        type: String,
        default: ''
    },
    status: {
        type: String,
        default: 'all'
    }
});

const products = ref<any[]>([]);
const activeForm = ref(false);
const isLoading = ref(true);
const selectedProduct = ref<any>(null);

const { handleApiError } = useNotification();

function modalFormProduct(product: any = null) {
    selectedProduct.value = product && product.productId ? product : null;
    activeForm.value = !activeForm.value;
}

defineExpose({
    modalFormProduct
});

const loadProducts = async () => {
    try {
        const productsResponse = await InventoryServices.getAllProducts();
        products.value = productsResponse.data.data.map((item: any) => ({
            barcode: item.barcode,
            description: item.description,
            productId: item.id_product,
            isActive: item.is_active,
            pendingVerification: item.pending_verification,
            product: item.product,
            salePrice: item.sale_price,
            supplier: item.supplier?.supplier || '',
            fk1_id_supplier: item.fk1_id_supplier || item.supplier?.id_supplier || '',
            totalPhysicalStock: item.total_physical_stock,
            unit: item.unit,
            original: item
        })).sort((a: any, b: any) => a.productId - b.productId);
    } catch (error: any) {
        handleApiError(error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    loadProducts();
});

const filteredProducts = computed(() => {
    return products.value.filter(p => {
        // Search filter
        const query = props.search.toLowerCase().trim();
        const matchesSearch = query === '' || 
                              String(p.product || '').toLowerCase().includes(query) || 
                              String(p.barcode || '').toLowerCase().includes(query);
        
        // Status filter
        let matchesStatus = true;
        if (props.status === 'active') matchesStatus = p.isActive;
        if (props.status === 'inactive') matchesStatus = !p.isActive;

        return matchesSearch && matchesStatus;
    });
});
</script>

<template>
    <main class="container">

        <ProductForm 
            v-if="activeForm" 
            :product="selectedProduct" 
            @close="modalFormProduct" 
            @saved="() => { loadProducts(); modalFormProduct(); }"
        />

        <section class="cards-container">
            <SkeletonCard v-if="isLoading" :count="6" />
            <template v-else>
                <section v-for="product in filteredProducts" :key="product.productId" class="card">
                <div class="card-header">
                    <h3>{{ product.product }}</h3>
                    <button :class="{ 'active': product.isActive, 'inactive': !product.isActive }">{{ product.isActive ? 'Activo' : 'Inactivo' }}</button>
                </div>
                <div class="data-card">
                    <section class="data-card-product">
                        <p>Stock actual: {{ product.totalPhysicalStock }}</p>
                        <p>Proveedor: {{ product.supplier }}</p>
                        <p>Unidad: {{ product.unit }}</p>
                    </section>

                    <section class="data-card-verification">
                        <p>Stock verificado: {{ product.pendingVerification ? 'Pendiente' : 'Verificado' }}</p>
                    </section>

                    <section class="data-card-sale">
                        <p>Precio: <br> $ {{ product.salePrice }}</p>
                        <p>{{ product.barcode }}</p>
                    </section>

                    <section class="data-card-buttons">
                        <button @click="modalFormProduct(product)" class="edit-button">Editar Producto</button>
                        <router-link :to="{ name: 'ticket', params: { id: product.productId, name: product.product } }" class="tickets-button">Ver Inventarios Registrados </router-link>
                    </section>
                    
                </div>
            </section>
            </template>
        </section>
    </main>
</template>

<style>
    @import '@/assets/styles/inventory/products.css';
</style>