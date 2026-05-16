<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DepartmentsServices from '@/services/DepartmentsServices';
import { useNotification } from '@/composables/useNotification';

const props = defineProps({
    department: {
        type: Object,
        default: null
    },
    storeId: {
        type: [Number, String],
        default: ''
    }
});

const emit = defineEmits(['close', 'saved']);

const isEditing = ref(false);
const formData = ref({
    department: '',
    description: '',
    fk1_id_general_dep: '' as number | string
});
const isSubmitting = ref(false);

const generalDepartments = ref<any[]>([]);
const isLoadingDeps = ref(false);
const { showWarning, handleApiError } = useNotification();

const sanitizeText = (value: string) => String(value || '').trim();

const loadGeneralDepartments = async () => {
    if (!props.storeId) return;
    
    isLoadingDeps.value = true;
    try {
        const response = await DepartmentsServices.getGeneralDepartmentsByStore(Number(props.storeId));
        generalDepartments.value = [...(response.data.data || response.data || [])].sort((a: any, b: any) =>
            Number(a.id_general_dep || a.id || 0) - Number(b.id_general_dep || b.id || 0)
        );
    } catch (error) {
        handleApiError(error);
    } finally {
        isLoadingDeps.value = false;
    }
};

onMounted(() => {
    loadGeneralDepartments();

    if (props.department) {
        isEditing.value = true;
        formData.value.department = props.department.department || props.department.name || '';
        formData.value.description = props.department.description || '';
        
        // Asignar el FK del general department dependiendo de cómo venga en la respuesta JSON
        formData.value.fk1_id_general_dep = props.department.fk1_id_general_dep 
            || props.department.generalDep?.id_general_dep 
            || props.department.general_dep?.id_general_dep
            || '';
    }
});

const closeModal = () => {
    emit('close');
};

const handleSubmit = async () => {
    const department = sanitizeText(formData.value.department);
    const description = sanitizeText(formData.value.description);

    if (!department || !description || !formData.value.fk1_id_general_dep) {
        showWarning('Datos incompletos', 'Por favor, completa todos los campos requeridos.');
        return;
    }

    if (department.length < 3 || department.length > 120) {
        showWarning('Nombre inválido', 'El nombre del departamento debe tener entre 3 y 120 caracteres.');
        return;
    }

    if (description.length < 5 || description.length > 255) {
        showWarning('Descripción inválida', 'La descripción debe tener entre 5 y 255 caracteres.');
        return;
    }

    isSubmitting.value = true;

    const payload = {
        ...formData.value,
        department,
        description,
    };

    try {
        if (isEditing.value && props.department) {
            await DepartmentsServices.updateDepartment(props.department.id_department, payload);
        } else {
            // El store_id no se envía al API ya que la relación principal es hacia general_deps
            await DepartmentsServices.createDepartment(payload);
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
                <h2>{{ isEditing ? 'Editar Departamento' : 'Nuevo Departamento' }}</h2>
                <button class="close-btn" @click="closeModal" title="Cerrar">&times;</button>
            </header>

            <form class="department-form" @submit.prevent="handleSubmit">
                
                <div class="form-group">
                    <label for="generalDepSelect">Departamento General</label>
                    <select 
                        id="generalDepSelect" 
                        v-model="formData.fk1_id_general_dep" 
                        required 
                        :disabled="isSubmitting || isLoadingDeps"
                    >
                        <option value="" disabled>-- Seleccione un dpto. general --</option>
                        <option v-for="gd in generalDepartments" :key="gd.id_general_dep" :value="gd.id_general_dep">
                            {{ gd.g_departament || gd.general_department || gd.name || gd.department || gd.id_general_dep }}
                        </option>
                    </select>
                    <small class="help-text">
                        <strong>Nota:</strong> El departamento general liga los departamentos de las tiendas para generar datos y estadísticas globales de todas las sucursales juntas.
                    </small>
                </div>

                <div class="form-group">
                    <label for="departmentName">Nombre del Departamento</label>
                    <input 
                        type="text" 
                        id="departmentName" 
                        v-model="formData.department" 
                        placeholder="Ej. Reclutamiento" 
                        required 
                        minlength="3"
                        maxlength="120"
                        :disabled="isSubmitting"
                    />
                </div>

                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea 
                        id="description" 
                        v-model="formData.description" 
                        placeholder="Funciones del departamento..." 
                        required 
                        minlength="5"
                        maxlength="255"
                        :disabled="isSubmitting"
                        rows="3"
                    ></textarea>
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
@import '@/assets/styles/data/components/departmentsForm.css';
</style>
