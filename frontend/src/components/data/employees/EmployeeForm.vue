<script setup lang="ts">
// Seccion: "Importaciones"
// Explicacion: Se importan las utilidades de Vue, el servicio de empleados para CRUD
//              y el servicio de sucursales para poblar el select del formulario
import { ref, onMounted, computed } from 'vue';
import EmployeeServices from '../../../services/EmployeeServices';
import StoresServices from '../../../services/StoresServices';

// Seccion: "Props y eventos"
// Explicacion: employeeId es opcional; si se recibe el componente actua en modo edicion,
//              si no, actua en modo alta; emite 'saved' al guardar y 'cancel' al cerrar
const props = defineProps<{
    employeeId?: number;
}>();

const emit = defineEmits<{
    (e: 'saved'): void;
    (e: 'cancel'): void;
}>();

// Seccion: "Estado reactivo"
// Explicacion: isEditMode se calcula automaticamente segun si hay employeeId;
//              loading bloquea el form mientras carga datos en modo edicion;
//              saving bloquea el boton mientras se envia la peticion
const isEditMode = computed(() => !!props.employeeId);
const loading = ref(false);
const saving = ref(false);
const errorMsg = ref('');
const stores = ref<Array<{ id_store: number; store: string }>>([]);

const form = ref({
    full_name: '',
    email: '',
    phone: '',
    document_type: '',
    document_number: '',
    position: '',
    salary: '',
    status: 'Active',
    hire_date: '',
    end_date: '',
    notes: '',
    fk_id_store: '' as string | number,
});

// Seccion: "Inicializacion del formulario"
// Explicacion: Al montar siempre carga las sucursales disponibles; si es modo edicion
//              ademas carga los datos del empleado y los rellena en el formulario
onMounted(async () => {
    try {
        const res = await StoresServices.getStores();
        stores.value = res.data.data;
    } catch {
        errorMsg.value = 'No se pudieron cargar las sucursales.';
    }

    if (isEditMode.value) {
        loading.value = true;
        try {
            const response = await EmployeeServices.getEmployee(props.employeeId!);
            const d = response.data.data;
            form.value = {
                full_name: d.full_name ?? '',
                email: d.email ?? '',
                phone: d.phone ?? '',
                document_type: d.document_type ?? '',
                document_number: d.document_number ?? '',
                position: d.position ?? '',
                salary: d.salary ?? '',
                status: d.status ?? 'Active',
                hire_date: d.hire_date ?? '',
                end_date: d.end_date ?? '',
                notes: d.notes ?? '',
                fk_id_store: d.store?.id_store ?? '',
            };
        } catch {
            errorMsg.value = 'No se pudo cargar el empleado.';
        } finally {
            loading.value = false;
        }
    }
});

// Seccion: "Envio del formulario"
// Explicacion: Construye el payload con los tipos correctos (salary y fk_id_store como Number),
//              luego llama a create o update segun el modo; emite 'saved' si tiene exito
async function handleSubmit() {
    saving.value = true;
    errorMsg.value = '';
    try {
        const payload = {
            ...form.value,
            salary: Number(form.value.salary),
            fk_id_store: Number(form.value.fk_id_store),
            end_date: form.value.end_date || null,
        };

        if (isEditMode.value) {
            await EmployeeServices.updateEmployee(props.employeeId!, payload as any);
        } else {
            await EmployeeServices.createEmployee(payload as any);
        }
        emit('saved');
    } catch {
        errorMsg.value = 'Error al guardar. Verifica los datos e intenta de nuevo.';
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <div class="modal-overlay" @click.self="emit('cancel')">
        <div class="modal-card">
            <div class="modal-header">
                <h2>{{ isEditMode ? 'Editar Empleado' : 'Nuevo Empleado' }}</h2>
                <button class="close-btn" @click="emit('cancel')">✕</button>
            </div>

            <div v-if="loading" class="loading-msg">Cargando datos...</div>

            <form v-else @submit.prevent="handleSubmit" class="employee-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input v-model="form.full_name" type="text" required placeholder="Nombre completo" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="email" required placeholder="correo@ejemplo.com" />
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input v-model="form.phone" type="text" placeholder="10 dígitos" />
                    </div>
                    <div class="form-group">
                        <label>Tipo de documento</label>
                        <select v-model="form.document_type" required>
                            <option value="" disabled>Seleccionar...</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Número de documento</label>
                        <input v-model="form.document_number" type="text" placeholder="Número" />
                    </div>
                    <div class="form-group">
                        <label>Puesto</label>
                        <select v-model="form.position" required>
                            <option value="" disabled>Seleccionar...</option>
                            <option value="Manager">Gerente</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Cashier">Cajero</option>
                            <option value="Stock">Almacén</option>
                            <option value="Sales">Ventas</option>
                            <option value="Other">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Salario</label>
                        <input v-model="form.salary" type="number" min="0" step="0.01" placeholder="0.00" />
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select v-model="form.status" required>
                            <option value="Active">Activo</option>
                            <option value="Inactive">Inactivo</option>
                            <option value="On Leave">De baja temporal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sucursal</label>
                        <select v-model.number="form.fk_id_store" required>
                            <option value="" disabled>Seleccionar...</option>
                            <option v-for="store in stores" :key="store.id_store" :value="store.id_store">
                                {{ store.store }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Fecha de ingreso</label>
                        <input v-model="form.hire_date" type="date" required />
                    </div>
                    <div class="form-group">
                        <label>Fecha de salida</label>
                        <input v-model="form.end_date" type="date" />
                    </div>
                    <div class="form-group form-group--full">
                        <label>Notas</label>
                        <textarea v-model="form.notes" rows="3" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </div>

                <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" @click="emit('cancel')">Cancelar</button>
                    <button type="submit" class="btn-save" :disabled="saving">
                        {{ saving ? 'Guardando...' : (isEditMode ? 'Actualizar' : 'Guardar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
@import '../../../assets/styles/data/components/employeeForm.css';
</style>
