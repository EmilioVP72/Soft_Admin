<script setup lang="ts">
// Seccion: "Importaciones"
// Explicacion: Se importan el servicio de sucursales, el formulario modal
//              y las utilidades de Vue necesarias para el estado reactivo
import StoresServices from '@/services/StoresServices';
import LocalitiesServices from '@/services/LocalitiesServices';
import StoreForm from './StoreForm.vue';
import { onMounted, ref } from 'vue';
import ErrorMessage from '@/components/shared/Error.vue';

// Seccion: "Estado reactivo"
// Explicacion: storeData almacena la lista de sucursales mostrada en la tabla;
//              showForm y selectedStoreId controlan la visibilidad del formulario
//              y si se abre en modo alta o edicion;
//              showDeleteConfirm y deleteTargetId gestionan el modal de confirmacion de borrado
const storeData = ref<Array<{ id: number; colony: string; exterior_number: string; interior_number: string; name: string; reference: string; street: string; fk_locality: number; }>>([]); 
const localities = ref<Array<{ id_locality: number; locality: string }>>([]);
const showForm = ref(false);
const selectedStoreId = ref<number | undefined>(undefined);
const showDeleteConfirm = ref(false);
const deleteTargetId = ref<number | null>(null);
const deleting = ref(false);
const error_data = ref<boolean>(false);

// Seccion: "Carga de datos"
// Explicacion: Llama al endpoint para obtener todas las sucursales y mapea
//              la respuesta al formato que utiliza la tabla
async function fetchStores() {
    try {
        const response = await StoresServices.getStores();
    
        storeData.value = response.data.data.map((store: { id: number; colony: string; exterior_number: string; interior_number: string; name: string; reference: string; street: string; fk1_id_locality: number }) => ({
            id: store.id,
            colony: store.colony,
            exterior_number: store.exterior_number,
            interior_number: store.interior_number,
            name: store.name,
            reference: store.reference,
            street: store.street,
            fk_locality: store.fk1_id_locality,
        }));

        const responseLocalities = await LocalitiesServices.getLocalities();
        localities.value = responseLocalities.data.data.map((loc: { id_locality: number; locality: string }) => ({
            id_locality: loc.id_locality,
            locality: loc.locality,
        }));
        
        error_data.value = false;
    } catch (error) {
        console.error('Error fetching store data:', error);
        error_data.value = true;
    }
}

// Seccion: "Control del formulario"
// Explicacion: openInsert abre el form en modo alta (sin ID); openEdit lo abre
//              en modo edicion con el ID de la sucursal seleccionada
function openInsert() {
    selectedStoreId.value = undefined;
    showForm.value = true;
}

function localityToId(id: number): string | null {
    
    var finded = localities.value.find(loc => loc.id_locality === id);
    if (finded) return finded.locality;
    return null;
}
function openEdit(id: number) {
    selectedStoreId.value = id;
    showForm.value = true;
}

// Seccion: "Control del modal de eliminacion"
// Explicacion: confirmDelete abre el dialog de confirmacion para el registro indicado;
//              cancelDelete lo cierra sin hacer nada;
//              executeDelete llama al endpoint y recarga la tabla si tiene exito
function confirmDelete(id: number) {
    deleteTargetId.value = id;
    showDeleteConfirm.value = true;
}

function cancelDelete() {
    showDeleteConfirm.value = false;
    deleteTargetId.value = null;
}

async function executeDelete() {
    if (deleteTargetId.value === null) return;
    deleting.value = true;
    try {
        await StoresServices.deleteStore(deleteTargetId.value);
        showDeleteConfirm.value = false;
        deleteTargetId.value = null;
        await fetchStores();
    } catch (error) {
        console.error('Error al eliminar la sucursal:', error);
    } finally {
        deleting.value = false;
    }
}



// Seccion: "Callbacks del formulario"
// Explicacion: onSaved cierra el formulario y recarga la tabla tras guardar;
//              onCancel cierra el formulario sin hacer cambios
async function onSaved() {
    showForm.value = false;
    await fetchStores();
}

function onCancel() {
    showForm.value = false;
}

// Seccion: "Impresion"
// Explicacion: Abre el reporte PDF de sucursales en una nueva pestaña
function printReport() {
    window.open('http://localhost:8000/api/reports/stores', '_blank');
}

// Seccion: "Inicializacion"
// Explicacion: Al montar el componente se cargan las sucursales automaticamente
onMounted(fetchStores);

</script>
<template>

    <ErrorMessage v-if="error_data" tittle="Error al cargar los datos de las sucursales" message="No se pudieron cargar los datos de las sucursales. Por favor, intenta de nuevo más tarde. Si el problema persiste, contacta al soporte." />

    <div v-else class="data-view">
        <div class="toolbar">
            <h1>Datos de las Sucursales</h1>
            <div class="toolbar-actions">
                <button class="btn-new" @click="openInsert">+ Nueva Sucursal</button>
                <button class="btn-print">Imprimir Reporte</button>
            </div>
        </div>

        <StoreForm
            v-if="showForm"
            :store-id="selectedStoreId"
            @saved="onSaved"
            @cancel="onCancel"
        />

        <!-- Modal de confirmacion de eliminacion -->
        <div v-if="showDeleteConfirm" class="delete-overlay" @click.self="cancelDelete">
            <div class="delete-card">
                <div class="delete-header">
                    <span>⚠ Eliminar Sucursal</span>
                </div>
                <div class="delete-body">
                    <p>¿Estás seguro de que deseas eliminar esta sucursal?</p>
                    <p class="delete-warning">Esta acción no se puede deshacer.</p>
                </div>
                <div class="delete-actions">
                    <button class="btn-cancel-delete" @click="cancelDelete" :disabled="deleting">Cancelar</button>
                    <button class="btn-confirm-delete" @click="executeDelete" :disabled="deleting">
                        {{ deleting ? 'Eliminando...' : 'Sí, eliminar' }}
                    </button>
                </div>
            </div>
        </div>

        <div class="table-section">
        <table class="stores-table">
            <thead class="table-header">
                <tr class="header-row">
                    <th class="header-cell">No</th>
                    <th class="header-cell">Nombre</th>
                    <th class="header-cell">Calle</th>
                    <th class="header-cell">Número Exterior</th>
                    <th class="header-cell">Número Interior</th>
                    <th class="header-cell">Colonia</th>
                    <th class="header-cell">Localidad</th>
                    <th class="header-cell">Referencia</th>
                    <th class="header-cell">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-body">
                <tr v-for="store in storeData" :key="store.id" class="data-row">
                    <td class="data-cell">{{ store.id }}</td>
                    <td class="data-cell">{{ store.name }}</td>
                    <td class="data-cell">{{ store.street }}</td>
                    <td class="data-cell">{{ store.exterior_number }}</td>
                    <td class="data-cell">{{ store.interior_number || 'S/N' }}</td>
                    <td class="data-cell">{{ store.colony }}</td>
                    <td class="data-cell">{{ localityToId(store.fk_locality) }}</td>
                    <td class="data-cell">{{ store.reference }}</td>
                    <td class="data-cell">
                        <button class="action-button edit-button" @click="openEdit(store.id)">Editar</button>
                        <button class="action-button delete-button" @click="confirmDelete(store.id)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
</template>

<style  scoped>
@import '@/assets/styles/data/components/stores.css';
</style>