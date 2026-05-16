<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import InventoryServices from '@/services/InventoryServices';
import { useNotification } from '@/composables/useNotification';
import SkeletonCard from '@/components/shared/SkeletonCard.vue';
import EmptyState from '@/components/shared/EmptyState.vue';

const route = useRoute();
const { showError, showSuccess, handleApiError } = useNotification();

const registers = ref<any[]>([]);
const isLoading = ref(true);

// State for new ticket modal
const showNewTicketModal = ref(false);
const newTicket = ref({
    ticket_quantity: null as number | null,
    ticket_date: new Date().toISOString().slice(0, 16)
});

const loadTickets = async () => {
    try{
        const response = await InventoryServices.getAllRegistersOfProduct(Number(route.params.id));
        registers.value = response.data.data.map((item: any) => ({    
            ticketId: item.id_product_inventory,  
            quantity: item.ticket_quantity,
            physicalQuantity: item.physical_quantity,
            difference: item.difference,
            status: item.status,
            notes: item.notes,
            ticketDate: item.ticket_date,
            verifiedAt: item.verified_at,
            isEditing: false
        })).sort((a: any, b: any) => {
            const d1 = new Date(a.ticketDate.replace(' ', 'T')).getTime();
            const d2 = new Date(b.ticketDate.replace(' ', 'T')).getTime();
            return (isNaN(d2) ? 0 : d2) - (isNaN(d1) ? 0 : d1);
        });
        
        
    }catch(error: any){
        handleApiError(error);
    }finally{
        isLoading.value = false;
    }
}

const parseDate = (dateString: string) => {
    if (!dateString) return new Date(NaN);
    let d = new Date(dateString);
    if (isNaN(d.getTime())) {
        d = new Date(dateString.replace(' ', 'T'));
    }
    return d;
}

const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = parseDate(dateString);
    if (isNaN(date.getTime())) return dateString.split(' ')[0] || dateString;
    return date.toLocaleDateString('es-MX', { year: 'numeric', month: '2-digit', day: '2-digit' });
}

const formatTime = (dateString: string) => {
    if (!dateString) return '';
    const date = parseDate(dateString);
    if (isNaN(date.getTime())) return dateString.split(' ')[1] || '';
    return date.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', hour12: true });
}

const mapStatus = (status: string) => {
    switch(status) {
        case 'pending': return 'Pendiente';
        case 'discrepancy': return 'Inconsistencia';
        case 'verified': return 'Verificado';
        default: return status;
    }
}

// Logic to create a new ticket
const submitNewTicket = async () => {
    if (newTicket.value.ticket_quantity === null || newTicket.value.ticket_quantity <= 0) {
        showError('Validación', 'Debe ingresar una cantidad esperada válida (mayor a cero).');
        return;
    }
    
    try {
        const payload = {
            fk1_id_product: Number(route.params.id),
            ticket_quantity: newTicket.value.ticket_quantity,
            status: 'pending',
            ticket_date: newTicket.value.ticket_date.replace('T', ' ') + ':00'
        };
        await InventoryServices.createNewTicket(payload);
        showNewTicketModal.value = false;
        
        // Reset form
        newTicket.value.ticket_quantity = null;
        newTicket.value.ticket_date = new Date().toISOString().slice(0, 16);
        
        // Reload list
        loadTickets();
        showSuccess('Ticket Registrado', 'El nuevo ticket se ha creado correctamente.');
    } catch (error: any) {
        handleApiError(error);
    }
}

// Logic to verify a ticket
const verifyTicket = async (register: any) => {
    if (register.physicalQuantity === null || register.physicalQuantity === undefined || register.physicalQuantity < 0) {
        showError('Validación', 'Debe ingresar una cantidad física válida antes de verificar.');
        return;
    }

    try {
        const payload = {
            physical_quantity: register.physicalQuantity,
            notes: register.notes || ''
        };
        await InventoryServices.verifyInventory(register.ticketId, payload);
        
        // Reload list
        loadTickets();
        showSuccess('Verificación Exitosa', 'El ticket ha sido verificado y actualizado.');
    } catch (error: any) {
        handleApiError(error);
    }
}

onMounted( async () => {
  loadTickets();  
})


</script>

<template>
    <main class="container">
        <section class="header">
            <h1>Historial de Registros de <span>{{ route.params.name }}</span></h1>
            <div class="buttons">
                <button @click="showNewTicketModal = true">Registrar Nuevo ticket</button>

            </div>
        </section>

        <!-- Modal para nuevo ticket -->
        <div v-if="showNewTicketModal" class="modal-overlay" @click.self="showNewTicketModal = false">
            <div class="modal-content">
                <h2>Registrar Nuevo Ticket</h2>
                <div class="form-group">
                    <label>Producto</label>
                    <input type="text" :value="productName" disabled>
                </div>
                <div class="form-group">
                    <label>Estado Inicial</label>
                    <input type="text" value="Pendiente" disabled>
                </div>
                <div class="form-group">
                    <label>Fecha y Hora del Ticket</label>
                    <input type="datetime-local" v-model="newTicket.ticket_date">
                </div>
                <div class="form-group">
                    <label>Cantidad Esperada</label>
                    <input type="number" v-model="newTicket.ticket_quantity" placeholder="Ingrese la cantidad">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" @click="showNewTicketModal = false">Cancelar</button>
                    <button type="button" class="btn-confirm" @click="submitNewTicket">Guardar</button>
                </div>
            </div>
        </div>

        <section class="tickets">
            <SkeletonCard v-if="isLoading" :count="4" />
            <EmptyState 
                v-else-if="registers.length === 0"
                title="Historial Vacío"
                message="No se encontró ningún registro en el historial para este producto."
                iconClass="fas fa-clipboard-list"
            />
            <template v-else>
                <div v-for="register in registers" :key="register.ticketId" :class="['ticket', register.status]">
                    <section class="ticket-data">
                        <div class="ticket-status">
                            <h3>Fecha: {{ formatDate(register.ticketDate) }} | Hora: {{ formatTime(register.ticketDate) }}</h3>
                            <p v-if="register.verifiedAt">Verificación: {{ formatDate(register.verifiedAt) }} | Hora: {{ formatTime(register.verifiedAt) }}</p>
                            <p v-else>Verificación: Aún no verificado</p>
                        <div class="status-container">
                            <h3 :class="['status-badge', register.status]">{{ mapStatus(register.status) }}</h3>
                            <button v-if="register.status === 'discrepancy' && !register.isEditing" class="btn-edit-status" @click="register.isEditing = true" title="Editar discrepancia">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>

                    <div class="ticket-data-register">
                        <p>Cantidad Esperada: {{ register.quantity }}</p>
                        <div v-if="register.status == 'pending' || register.isEditing" class="register-inputs">
                            <input type="number" v-model="register.physicalQuantity" placeholder="Cantidad Actual (Física)">
                        </div>
                        <div v-else>
                            <p>Cantidad Actual: {{ register.physicalQuantity }}</p>
                        </div>
                    </div>

                    <div class="ticket-data-verification">
                        <div v-if="register.status == 'pending' || register.isEditing" class="verification-actions">
                            <textarea v-model="register.notes" placeholder="Añadir notas u observaciones (opcional)..."></textarea>
                            <button @click="verifyTicket(register)">Guardar y Verificar</button>
                        </div>
                        <div v-else>
                            <p>Diferencia: {{ register.difference  || 0}}</p>
                            <p>{{ register.notes || 'No se han registrado notas o aún no se verifica el registro' }}</p>
                        </div>
                    </div>
                </section>
            </div>
            </template>
        </section>


    </main>
</template>

<style scoped>
    @import '@/assets/styles/inventory/registers.css';
</style>
