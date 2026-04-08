<script setup lang="ts">
import { ref, onMounted } from 'vue';
import DepartmentsServices from '@/services/DepartmentsServices';

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
const errorMsg = ref('');
const isSubmitting = ref(false);

const generalDepartments = ref<any[]>([]);
const isLoadingDeps = ref(false);

const loadGeneralDepartments = async () => {
    if (!props.storeId) return;
    
    isLoadingDeps.value = true;
    try {
        const response = await DepartmentsServices.getGeneralDepartmentsByStore(Number(props.storeId));
        generalDepartments.value = response.data.data || response.data;
    } catch (error) {
        console.error("Error cargando departamentos generales:", error);
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
    if (!formData.value.department || !formData.value.description || !formData.value.fk1_id_general_dep) {
        errorMsg.value = 'Por favor, completa todos los campos requeridos.';
        return;
    }

    errorMsg.value = '';
    isSubmitting.value = true;

    try {
        if (isEditing.value && props.department) {
            await DepartmentsServices.updateDepartment(props.department.id_department, formData.value);
        } else {
            // El store_id no se envía al API ya que la relación principal es hacia general_deps
            await DepartmentsServices.createDepartment(formData.value);
        }
        emit('saved');
    } catch (error: any) {
        console.error("Error guardando el departamento:", error);
        errorMsg.value = error.response?.data?.message || 'Ocurrió un error al guardar el departamento. Intenta de nuevo.';
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
                        :disabled="isSubmitting"
                        rows="3"
                    ></textarea>
                </div>

                <!-- Mensaje de error -->
                <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>

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
