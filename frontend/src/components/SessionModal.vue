<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';

// Props
defineProps<{
  isLoading: boolean;
}>();

// Emits
const emit = defineEmits<{
  (e: 'confirm'): void;
  (e: 'cancel'): void;
}>();

// Referencias para accesibilidad
const modalRef = ref<HTMLElement>();
const previousActiveElement = ref<HTMLElement>();

// 1. GUARDAR ELEMENTO ACTIVO Y AUTO-FOCUS
onMounted(() => {
  previousActiveElement.value = document.activeElement as HTMLElement;
  
  // Bloquear scroll del body
  document.body.classList.add('modal-open');
  
  // Auto-focus al primer botón
  setTimeout(() => {
    const firstButton = modalRef.value?.querySelector('button');
    firstButton?.focus();
  }, 100);
});

// 2. RESTAURAR FOCUS Y DESBLOQUEAR SCROLL
onUnmounted(() => {
  document.body.classList.remove('modal-open');
  previousActiveElement.value?.focus();
});

// 3. MANEJAR TECLA ESC
const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    emit('cancel');
  }
};

// 4. AGREGAR/REMOVER LISTENER
onMounted(() => {
  document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
  <div 
    class="modal-overlay"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
    aria-describedby="modal-desc"
    ref="modalRef"
  >
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modal-title">⚠️ Tu sesión está por expirar</h3>
      </div>
      
      <div class="modal-body">
        <p id="modal-desc">
          Por seguridad, tu sesión se cerrará en menos de 5 minutos.
          ¿Deseas mantenerte conectado?
        </p>
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