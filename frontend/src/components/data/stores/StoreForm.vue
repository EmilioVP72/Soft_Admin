<script setup lang="ts">
// Seccion: "Importaciones"
// Explicacion: Se importan las utilidades de Vue y el servicio de sucursales
//              para ejecutar las peticiones de creacion y edicion
import { ref, onMounted, computed } from 'vue';
import StoresServices from '@/services/StoresServices';
import LocalitiesServices from '@/services/LocalitiesServices';

// Seccion: "Props y eventos"
// Explicacion: storeId es opcional; si se recibe el componente actua en modo edicion,
//              si no actua en modo alta; emite 'saved' al guardar y 'cancel' al cerrar
const props = defineProps<{
    storeId?: number;
}>();

const emit = defineEmits<{
    (e: 'saved'): void;
    (e: 'cancel'): void;
}>();

// Seccion: "Estado reactivo"
// Explicacion: isEditMode se calcula segun si hay storeId; loading bloquea el form
//              mientras carga datos en modo edicion; saving bloquea el boton de guardar
const isEditMode = computed(() => !!props.storeId);
const loading = ref(false);
const saving = ref(false);
const errorMsg = ref('');
const localities = ref<Array<{ id_locality: number; locality: string }>>([]);

const form = ref({
    store: '',
    street: '',
    exterior_number: '',
    interior_number: '' as string | null,
    colony: '',
    reference: '',
    fk1_id_locality: '' as string | number,
});

// Seccion: "Inicializacion del formulario"
// Explicacion: Si es modo edicion carga los datos de la sucursal desde el backend
//              y los rellena en el formulario para que el usuario los modifique
onMounted(async () => {
    try {
        const res = await LocalitiesServices.getLocalities();
        localities.value = res.data.data;
    } catch {
        errorMsg.value = 'No se pudieron cargar las localidades.';
    }

    if (isEditMode.value) {
        loading.value = true;
        try {
            console.log('Cargando datos para la sucursal con ID:', props.storeId);
            const response = await StoresServices.getOneStore(props.storeId!);
            const d = response.data.data;
            console.log('Datos de la sucursal obtenidos:', d.fk1_id_locality);
            form.value = {
                store: d.store ?? d.name ?? '',
                street: d.street ?? '',
                exterior_number: String(d.exterior_number ?? ''),
                interior_number: d.interior_number ? d.interior_number : null,
                colony: d.colony ?? '',
                reference: d.reference ?? '',
                fk1_id_locality: d.fk1_id_locality ?? '',
            };
        } catch {
            errorMsg.value = 'No se pudo cargar la sucursal.';
        } finally {
            loading.value = false;
        }
    }
});

// Seccion: "Envio del formulario"
// Explicacion: Llama a createStore o updateStore segun el modo;
//              emite 'saved' si tiene exito o muestra el mensaje de error si falla
async function handleSubmit() {
    saving.value = true;
    errorMsg.value = '';
    try {
        const payload = {
            ...form.value,
            exterior_number: String(form.value.exterior_number),
            interior_number: form.value.interior_number ? String(form.value.interior_number) : null,
        };
        if (isEditMode.value) {
            await StoresServices.updateStore(props.storeId!, payload as any);
        } else {
            await StoresServices.createStore(payload as any);
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
                <h2>{{ isEditMode ? 'Editar Sucursal' : 'Nueva Sucursal' }}</h2>
                <button class="close-btn" @click="emit('cancel')">✕</button>
            </div>

            <div v-if="loading" class="loading-msg">Cargando datos...</div>

            <form v-else @submit.prevent="handleSubmit" class="store-form">
                <div class="form-grid">
                    <div class="form-group form-group--full">
                        <label>Nombre de la Sucursal</label>
                        <input v-model="form.store" type="text" required placeholder="Ej. Sucursal Centro" />
                    </div>
                    <div class="form-group">
                        <label>Calle</label>
                        <input v-model="form.street" type="text" required placeholder="Nombre de la calle" />
                    </div>
                    <div class="form-numbers">
                        <div class="form-group">
                            <label>Número Exterior</label>
                            <input v-model="form.exterior_number" type="text" required placeholder="Ej. 123" />
                        </div>
                        <div class="form-group">
                            <label>Número Interior</label>
                            <input v-model="form.interior_number " type="text" placeholder="Ej. 4-B (opcional)" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Colonia</label>
                        <input v-model="form.colony" type="text" required placeholder="Nombre de la colonia" />
                    </div>
                    <div class="form-group">
                        <label>Localidad</label>
                        <select v-model.number="form.fk1_id_locality" required>
                            <option value="" disabled>Selecciona una localidad</option>
                            <option v-for="loc in localities" :key="loc.id_locality" :value="loc.id_locality">
                                {{ loc.locality }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group form-group--full">
                        <label>Referencia</label>
                        <textarea v-model="form.reference" rows="2" placeholder="Ej. Frente al parque central..."></textarea>
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
@import '@/assets/styles/data/components/storeForm.css';
</style>
