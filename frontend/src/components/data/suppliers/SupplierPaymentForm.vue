<script setup lang="ts">
import { ref, onMounted } from 'vue';
import SuppliersServices from '@/services/SuppliersServices';
import DepartmentsServices from '@/services/DepartmentsServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    supplier: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['close', 'saved']);
const { showWarning, showError } = useNotification();

const form = ref({
    fk2_id_department: '',
    amount_paid: '',
    payment_date: new Date().toISOString().split('T')[0]
});

const departments = ref<any[]>([]);
const isSubmitting = ref(false);

const sanitizeText = (value: string) => String(value || '').trim();

const validateForm = () => {
    const amount = Number(form.value.amount_paid);
    const today = new Date().toISOString().split('T')[0];

    if (!form.value.fk2_id_department || !form.value.payment_date || Number.isNaN(amount)) {
        showWarning('Datos incompletos', 'Todos los campos son obligatorios.');
        return false;
    }

    if (amount <= 0) {
        showWarning('Monto inválido', 'El monto pagado debe ser mayor a 0.');
        return false;
    }

    if (form.value.payment_date > today) {
        showWarning('Fecha inválida', 'La fecha de pago no puede ser futura.');
        return false;
    }

    return true;
};

onMounted(async () => {
    try {
        // Obtenemos los departamentos disponibles
        const res = await DepartmentsServices.getAllDepartments();
        // Dependiendo de si la respuesta viene envuelta en successResponse o es un array crudo
        departments.value = [...(res.data?.data || res.data || [])].sort((a: any, b: any) =>
            Number(a.id_department || a.id || 0) - Number(b.id_department || b.id || 0)
        );

        // Auto-seleccionar departamento si el proveedor ya tiene historial
        if (props.supplier.payments && props.supplier.payments.length > 0) {
            // Buscamos el departamento del último/primer pago
            const lastPayment = props.supplier.payments[props.supplier.payments.length - 1]; 
            if (lastPayment.department && lastPayment.department.id_department) {
                form.value.fk2_id_department = lastPayment.department.id_department;
            }
        } else if (departments.value.length > 0) {
            // Por defecto selecciona el primero
            form.value.fk2_id_department = departments.value[0].id_department;
        }

    } catch (e) {
        showError('Error al cargar', 'Error al cargar departamentos.');
    }
});

const submitForm = async () => {
    if (!validateForm()) return;

    isSubmitting.value = true;
    
    try {
        await SuppliersServices.storePayment(props.supplier.id, {
            ...form.value,
            amount_paid: String(Number(form.value.amount_paid)),
            payment_date: sanitizeText(form.value.payment_date),
        });
        emit('saved');
    } catch (error: any) {
        showError('Error al registrar', error.response?.data?.message || 'Error al registrar el pago.');
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
                <h2 class="text-xl font-bold">Agregar Pago a Proveedor</h2>
                <button @click="closeForm" class="text-slate-300 hover:text-white transition-colors" title="Cerrar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div class="mb-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100 flex gap-2 items-center">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m3-4h1m-1 4h1m-5 8h8"></path></svg>
                    Proveedor seleccionado: <strong class="text-slate-800 ml-1">{{ props.supplier.name }}</strong>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Monto Pagado ($) <span class="text-red-500">*</span></label>
                        <input 
                            type="number" 
                            step="0.01"
                            min="0"
                            v-model="form.amount_paid" 
                            required 
                            placeholder="Ej. 1500.50"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#1179a2] focus:border-[#1179a2] outline-none transition-all"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha de Pago <span class="text-red-500">*</span></label>
                        <input 
                            type="date" 
                            v-model="form.payment_date" 
                            required 
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#1179a2] focus:border-[#1179a2] outline-none transition-all"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Departamento <span class="text-red-500">*</span></label>
                        <select 
                            v-model="form.fk2_id_department" 
                            required
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-[#1179a2] focus:border-[#1179a2] outline-none transition-all bg-white"
                        >
                            <option value="" disabled>Seleccione un departamento...</option>
                            <option v-for="dept in departments" :key="dept.id_department" :value="dept.id_department">
                                {{ dept.department }}
                            </option>
                        </select>
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
                            class="px-5 py-2 text-sm font-medium text-white bg-[#10b981] rounded-lg hover:bg-[#059669] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center min-w-[110px]"
                        >
                           <svg v-if="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                           <span v-if="!isSubmitting">Registrar</span>
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
