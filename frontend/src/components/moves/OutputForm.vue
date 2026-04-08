<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import StoresServices from '@/services/StoresServices';
import LoginServices from '@/services/LoginServices';
import PaymentServices from '@/services/PaymentServices';
import DepartmentsServices from '@/services/DepartmentsServices';
import MovesServices from '@/services/MovesServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    isOpen: { type: Boolean, required: true }
});

const emit = defineEmits(['close', 'saved']);

// Catalogos
const storesList = ref<any[]>([]);
const paymentsList = ref<any[]>([]);
const departmentsList = ref<any[]>([]);

// Form principal
const formData = ref({
    fk1_id_store: '',
    fk2_id_user: '',
    fk3_id_payment: '',
    notes: '',
    transaction_date: '',
    details: [
        { fk2_id_department: '', quantity: 1, unit_price: 0, subtotal: 0 }
    ]
});

const isSubmitting = ref(false);
const { showWarning, showError } = useNotification();

const sanitizeText = (value: string) => String(value || '').trim();

const validateForm = () => {
    if (!formData.value.fk1_id_store || !formData.value.fk3_id_payment || !formData.value.fk2_id_user) {
        showWarning('Datos incompletos', 'Debes seleccionar tienda, tipo de pago y usuario.');
        return false;
    }

    if (formData.value.details.length === 0) {
        showWarning('Sin detalles', 'Debes agregar al menos un detalle para la salida.');
        return false;
    }

    const hasInvalidDetail = formData.value.details.some((d: any) => {
        const qty = Number(d.quantity) || 0;
        const unit = Number(d.unit_price) || 0;
        const sub = Number(d.subtotal) || 0;
        return !d.fk2_id_department || qty <= 0 || unit <= 0 || sub <= 0;
    });

    if (hasInvalidDetail) {
        showWarning('Detalle inválido', 'Cada detalle debe tener departamento, cantidad mayor a 0 y precio unitario mayor a 0.');
        return false;
    }

    if (Number(totalAmount.value) <= 0) {
        showWarning('Total inválido', 'El total de la operación debe ser mayor a 0.');
        return false;
    }

    if (sanitizeText(formData.value.notes).length > 500) {
        showWarning('Notas demasiado largas', 'Las notas no deben exceder 500 caracteres.');
        return false;
    }

    return true;
};

const totalAmount = computed(() => {
    return formData.value.details.reduce((acc, curr) => acc + (Number(curr.subtotal) || 0), 0);
});

// Calculate subtotal automatically
const updateSubtotal = (index: number) => {
    const detail = formData.value.details[index];
    detail.subtotal = (Number(detail.quantity) || 0) * (Number(detail.unit_price) || 0);
};

const addDetail = () => {
    formData.value.details.push({ fk2_id_department: '', quantity: 1, unit_price: 0, subtotal: 0 });
};

const removeDetail = (index: number) => {
    if (formData.value.details.length > 1) {
        formData.value.details.splice(index, 1);
    }
};

const onStoreChange = async () => {
    formData.value.details.forEach(d => d.fk2_id_department = '');
    if (!formData.value.fk1_id_store) {
        departmentsList.value = [];
        return;
    }
    try {
        const res = await DepartmentsServices.getDepartmentsByStore(Number(formData.value.fk1_id_store));
        departmentsList.value = res.data?.data || res.data || [];
    } catch (e) {
        showError('Error al cargar', 'No se pudieron cargar los departamentos de la sucursal.');
    }
};

const fetchCatalogs = async () => {
    try {
        const [storesRes, paysRes, meRes] = await Promise.all([
            StoresServices.getStores(),
            PaymentServices.getPayments(),
            LoginServices.meUser()
        ]);
        storesList.value = storesRes.data?.data || storesRes.data || [];
        paymentsList.value = paysRes.data?.data || paysRes.data || [];

        const userData = meRes.data?.data || meRes.data || {};
        formData.value.fk2_id_user = userData.id_user || userData.id;
    } catch (error) {
        showError('Error de catálogos', 'No se pudieron cargar opciones del formulario.');
    }
};

const submitForm = async () => {
    if (!validateForm()) return;

    isSubmitting.value = true;
    try {
        // Build payload matching Backend validation StoreOutputRequest
        const payload = {
            fk1_id_store: Number(formData.value.fk1_id_store),
            fk2_id_user: Number(formData.value.fk2_id_user),
            fk3_id_payment: Number(formData.value.fk3_id_payment),
            total_amount: Number(totalAmount.value),
            notes: sanitizeText(formData.value.notes) || null,
            transaction_date: formData.value.transaction_date || null,
            details: formData.value.details.map(d => ({
                fk2_id_department: d.fk2_id_department,
                quantity: Number(d.quantity),
                unit_price: Number(d.unit_price),
                subtotal: Number(d.subtotal)
            }))
        };

        await MovesServices.createOutput(payload);
        emit('saved');
        closeModal();
    } catch (error) {
        showError('Error al guardar', 'Ocurrió un error guardando la salida. Verifica los datos o intenta más tarde.');
    } finally {
        isSubmitting.value = false;
    }
};

const closeModal = () => {
    // Reset form
    formData.value = {
        fk1_id_store: '',
        fk2_id_user: '',
        fk3_id_payment: '',
        notes: '',
        transaction_date: '',
        details: [{ fk2_id_department: '', quantity: 1, unit_price: 0, subtotal: 0 }]
    };
    emit('close');
};

onMounted(() => {
    fetchCatalogs();
});
</script>

<template>
    <div v-if="isOpen" class="modal-overlay">
        <div class="modal-content">
            <header class="modal-header">
                <h2>Registrar Nueva Salida</h2>
                <button type="button" class="close-btn" @click="closeModal">&times;</button>
            </header>
            
            <form @submit.prevent="submitForm" class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tienda</label>
                        <select v-model="formData.fk1_id_store" required class="form-control" @change="onStoreChange">
                            <option value="">Seleccione Tienda</option>
                            <option v-for="s in storesList" :key="s.id_store" :value="s.id_store">{{ s.store || s.name }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Pago</label>
                        <select v-model="formData.fk3_id_payment" required class="form-control">
                            <option value="">Seleccione Pago</option>
                            <option v-for="p in paymentsList" :key="p.id_payment" :value="p.id_payment">{{ p.payment }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Fecha <small>(Opcional)</small></label>
                        <input type="date" v-model="formData.transaction_date" class="form-control">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Notas</label>
                        <textarea v-model="formData.notes" class="form-control" rows="2" placeholder="Notas de la salida..." maxlength="500"></textarea>
                </div>

                <hr class="separator" />
                <h3>Detalles de la Salida</h3>
                
                <div class="details-container">
                    <div v-for="(detail, index) in formData.details" :key="index" class="detail-row">
                        <div class="form-group flex-2">
                            <label>Departamento</label>
                            <select v-model="detail.fk2_id_department" required class="form-control">
                                <option value="">Seleccione depto.</option>
                                <option v-for="d in departmentsList" :key="d.id_department" :value="d.id_department">{{ d.department }}</option>
                            </select>
                        </div>
                        <div class="form-group flex-1">
                            <label>Cant.</label>
                            <input type="number" step="1" min="1" v-model="detail.quantity" required @input="updateSubtotal(index)" class="form-control">
                        </div>
                        <div class="form-group flex-1">
                            <label>Precio Unit.</label>
                            <input type="number" step="0.01" min="0.01" v-model="detail.unit_price" required @input="updateSubtotal(index)" class="form-control">
                        </div>
                        <div class="form-group flex-1">
                            <label>Subtotal</label>
                            <input type="number" v-model="detail.subtotal" readonly class="form-control readonly">
                        </div>
                        <button type="button" @click="removeDetail(index)" class="btn-remove" :disabled="formData.details.length === 1">X</button>
                    </div>
                    <button type="button" @click="addDetail" class="btn-add-detail">+ Agregar detalle</button>
                </div>

                <div class="total-section">
                    <h3>Total Estimado: <span class="highlight-total">${{ totalAmount.toFixed(2) }}</span></h3>
                </div>

                <footer class="modal-footer">
                    <button type="button" class="btn-cancel" @click="closeModal">Cancelar</button>
                    <button type="submit" class="btn-primary" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Guardando...' : 'Guardar Salida' }}
                    </button>
                </footer>
            </form>
        </div>
    </div>
</template>

<style scoped>
@import '@/assets/styles/moves/forms.css';
</style>
