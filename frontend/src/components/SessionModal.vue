<script setup lang="ts">

defineProps<{
  isLoading: boolean;
}>();

const emit = defineEmits<{
  (e: 'confirm'): void;
  (e: 'cancel'): void;
}>();
</script>

<template>
  <div class="modal-overlay">
    
    <div class="modal-content">
      <div class="modal-header">
        <h3>⚠️ Tu sesión está por expirar</h3>
      </div>
      
      <div class="modal-body">
        <p>Por seguridad, tu sesión se cerrará en menos de 5 minutos.</p>
        <p>¿Deseas mantenerte conectado?</p>
      </div>

      <div class="modal-actions">
        <button 
          @click="emit('cancel')" 
          class="btn btn-secondary"
        >
          Cerrar Sesión
        </button>

        <button 
          @click="emit('confirm')" 
          :disabled="isLoading"
          class="btn btn-primary"
        >
          {{ isLoading ? 'Renovando...' : 'Mantener Sesión' }}
        </button>
      </div>
    </div>

  </div>
</template>

<style src="@/assets/styles/base/SessionModal.css" scoped></style>