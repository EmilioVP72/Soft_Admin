<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DepartmentsServices from '@/services/DepartmentsServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    isEditForm: {
        type: Boolean,
        default: false
    },
    department: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['saved', 'close']);

const formData = ref({
    g_departament: '',
    g_descripcion: ''
});

const isSubmitting = ref(false);
const { showWarning, showSuccess, handleApiError } = useNotification();

const sanitizeText = (value: string) => String(value || '').trim();

onMounted(() => {
    if (props.isEditForm && props.department) {
        formData.value.g_departament = props.department.name || '';
        formData.value.g_descripcion = props.department.description || '';
    }
});

const closeModal = () => {
    emit('close');
}

const submitForm = async () => {
    const name = sanitizeText(formData.value.g_departament);
    const description = sanitizeText(formData.value.g_descripcion);

    if (!name || !description) {
        showWarning('Campos incompletos', 'Por favor, llena todos los campos obligatorios.');
        return;
    }

    if (name.length < 3) {
        showWarning('Nombre inválido', 'El nombre debe tener al menos 3 caracteres.');
        return;
    }

    isSubmitting.value = true;

    try {
        const payload = {
            g_departament: name,
            g_descripcion: description
        };

        if (props.isEditForm && props.department) {
            await DepartmentsServices.updateGeneralDepartment(props.department.id, payload);
            showSuccess('Actualizado', 'El departamento general se actualizó correctamente.');
        } else {
            await DepartmentsServices.createGeneralDepartment(payload);
            showSuccess('Creado', 'El departamento general se creó correctamente.');
        }
        
        emit('saved');
    } catch (error: any) {
        handleApiError(error);
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <div class="modal-overlay" @click.self="closeModal">
        <div class="modal-card">
            <header class="modal-header">
                <h2>{{ isEditForm ? 'Editar Departamento General' : 'Agregar Departamento General' }}</h2>
                <button class="close-btn" @click="closeModal" :disabled="isSubmitting">&times;</button>
            </header>
            <form class="department-form" @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" v-model="formData.g_departament" placeholder="Ej. Lácteos" required :disabled="isSubmitting" />
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <input type="text" id="description" v-model="formData.g_descripcion" placeholder="Breve descripción del departamento" required :disabled="isSubmitting" />
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" @click="closeModal" :disabled="isSubmitting">Cancelar</button>
                    <button type="submit" class="btn-save" :disabled="isSubmitting">{{ isSubmitting ? 'Guardando...' : 'Guardar' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@import '@/assets/styles/data/components/departmentsForm.css';
</style>