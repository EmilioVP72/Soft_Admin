<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import InventoryServices from '@/services/InventoryServices';
import SuppliersServices from '@/services/SuppliersServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    product: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'saved']);

const isEditing = ref(false);
const formData = ref({
    product: '',
    barcode: '',
    description: '',
    sale_price: 0,
    unit: '',
    is_active: true,
    fk1_id_supplier: '' as number | string
});
const isSubmitting = ref(false);
const isLoadingSuppliers = ref(false);
const suppliers = ref<any[]>([]);

const { showWarning, showSuccess, handleApiError } = useNotification();

const sanitizeText = (value: string) => String(value || '').trim();

const loadSuppliers = async () => {
    isLoadingSuppliers.value = true;
    try {
        const response = await SuppliersServices.getAllSuppliers();
        suppliers.value = response.data.data || response.data || [];
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoadingSuppliers.value = false;
    }
};

watch(() => props.product, (newVal) => {
    if (newVal && Object.keys(newVal).length > 0) {
        isEditing.value = true;
        formData.value.product = newVal.product || '';
        formData.value.barcode = newVal.barcode || '';
        formData.value.description = newVal.description || '';
        formData.value.sale_price = newVal.salePrice || 0;
        formData.value.unit = newVal.unit || '';
        formData.value.is_active = newVal.isActive ?? true;
        formData.value.fk1_id_supplier = newVal.fk1_id_supplier || newVal.original?.fk1_id_supplier || '';
    } else {
        isEditing.value = false;
        formData.value = {
            product: '',
            barcode: '',
            description: '',
            sale_price: 0,
            unit: '',
            is_active: true,
            fk1_id_supplier: ''
        };
    }
}, { immediate: true });

onMounted(() => {
    loadSuppliers();
});

const closeModal = () => {
    emit('close');
};

const handleSubmit = async () => {
    const product = sanitizeText(formData.value.product);
    const description = sanitizeText(formData.value.description);

    if (!product || !formData.value.fk1_id_supplier) {
        showWarning('Datos incompletos', 'Por favor, completa los campos obligatorios.');
        return;
    }

    isSubmitting.value = true;

    const payload = {
        ...formData.value,
        product,
        description,
    };

    try {
        if (isEditing.value && props.product) {
            await InventoryServices.updateProduct(props.product.productId, payload);
            showSuccess('Producto Actualizado', 'El producto se ha actualizado correctamente.');
        } else {
            await InventoryServices.createProduct(payload);
            showSuccess('Producto Creado', 'El nuevo producto se ha creado correctamente.');
        }
        emit('saved');
    } catch (error: any) {
        handleApiError(error);
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-card">
            <header class="modal-header">
                <h2>{{ isEditing ? 'Editar Producto' : 'Nuevo Producto' }}</h2>
                <button class="close-btn" @click="closeModal" title="Cerrar">&times;</button>
            </header>

            <form class="product-form" @submit.prevent="handleSubmit">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="productName">Nombre del Producto *</label>
                        <input 
                            type="text" 
                            id="productName" 
                            v-model="formData.product" 
                            placeholder="Ej. Coca Cola 600ml" 
                            required 
                            maxlength="255"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <div class="form-group">
                        <label for="barcode">Código de Barras</label>
                        <input 
                            type="text" 
                            id="barcode" 
                            v-model="formData.barcode" 
                            placeholder="Código escaneable" 
                            maxlength="100"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <div class="form-group">
                        <label for="supplierSelect">Proveedor *</label>
                        <select 
                            id="supplierSelect" 
                            v-model="formData.fk1_id_supplier" 
                            required 
                            :disabled="isSubmitting || isLoadingSuppliers"
                        >
                            <option value="" disabled>-- Seleccione un proveedor --</option>
                            <option v-for="sup in suppliers" :key="sup.id_supplier" :value="sup.id_supplier">
                                {{ sup.supplier || sup.name || sup.id_supplier }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="salePrice">Precio de Venta</label>
                        <input 
                            type="number" 
                            step="0.01"
                            id="salePrice" 
                            v-model="formData.sale_price" 
                            placeholder="0.00" 
                            min="0"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <div class="form-group">
                        <label for="unit">Unidad (Pza, Kg, Lts)</label>
                        <input 
                            type="text" 
                            id="unit" 
                            v-model="formData.unit" 
                            placeholder="Ej. Pza" 
                            maxlength="50"
                            :disabled="isSubmitting"
                        />
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Descripción</label>
                        <textarea 
                            id="description" 
                            v-model="formData.description" 
                            placeholder="Detalles adicionales del producto..." 
                            :disabled="isSubmitting"
                            rows="2"
                        ></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label class="checkbox-label">
                            <input 
                                type="checkbox" 
                                v-model="formData.is_active" 
                                :disabled="isSubmitting"
                            />
                            Producto Activo
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" @click="closeModal" :disabled="isSubmitting">Cancelar</button>
                    <button type="submit" class="btn-save" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@import '@/assets/styles/inventory/productForm.css';
</style>