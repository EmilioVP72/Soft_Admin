<script setup lang="ts">
import { ref, onMounted } from 'vue';
import SuppliersServices from '@/services/SuppliersServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    supplier: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close', 'saved']);
const { showWarning, handleApiError } = useNotification();

const supplierForm = ref({
    supplier: ''
});

onMounted(() => {
    if (props.supplier) {
        supplierForm.value.supplier = props.supplier.name;
    }
});

const isSubmitting = ref(false);

const sanitizeText = (value: string) => String(value || '').trim();

const submitForm = async () => {
    const supplierName = sanitizeText(supplierForm.value.supplier);
    if (!supplierName) {
        showWarning('Campo obligatorio', 'El nombre del proveedor es obligatorio.');
        return;
    }

    if (supplierName.length < 3 || supplierName.length > 120) {
        showWarning('Dato inválido', 'El nombre del proveedor debe tener entre 3 y 120 caracteres.');
        return;
    }

    isSubmitting.value = true;
    
    try {
        if (props.supplier) {
            await SuppliersServices.updateSupplier(props.supplier.id, { supplier: supplierName });
        } else {
            await SuppliersServices.createSupplier({ supplier: supplierName });
        }
        emit('saved');
    } catch (error: any) {
        handleApiError(error);
    } finally {
        isSubmitting.value = false;
    }
};

const closeForm = () => {
    emit('close');
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all" @click.stop>
            <!-- Header -->
            <div class="bg-[#1e293b] px-6 py-4 flex justify-between items-center text-white">
                <h2 class="text-xl font-bold">{{ props.supplier ? 'Editar Proveedor' : 'Agregar Nuevo Proveedor' }}</h2>
                <button @click="closeForm" class="text-slate-300 hover:text-white transition-colors" title="Cerrar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Proveedor <span class="text-red-500">*</span></label>
                        <input 
                            type="text" 
                            v-model="supplierForm.supplier" 
                            required 
                            placeholder="Ej. Coca-Cola, Bimbo, Lala..."
                            minlength="3"
                            maxlength="120"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#1179a2] focus:border-[#1179a2] outline-none transition-all"
                        >
                    </div>

                    <!-- Footer / Actions -->
                    <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <button 
                            type="button" 
                            @click="closeForm"
                            class="px-5 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="px-5 py-2 text-sm font-medium text-white bg-[#1179a2] rounded-lg hover:bg-[#0e6689] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[110px]"
                        >
                           <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                           <span v-if="!isSubmitting">{{ props.supplier ? 'Actualizar' : 'Guardar' }}</span>
                           <span v-else>Guardando</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
</style>