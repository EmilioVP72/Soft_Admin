<script setup lang="ts">
// Seccion: "Props y eventos"
// Explicacion: Componente generico reutilizable para cualquier confirmacion;
//              recibe el mensaje y opciones de texto/color via props;
//              con danger=true el header y boton de confirmar se vuelven rojos;
//              emite 'confirm' o 'cancel' segun la decision del usuario
defineProps<{
    title?: string;
    message: string;
    confirmLabel?: string;
    cancelLabel?: string;
    danger?: boolean;
}>();

const emit = defineEmits<{
    (e: 'confirm'): void;
    (e: 'cancel'): void;
}>();
</script>

<template>
    <div class="confirm-overlay" @click.self="emit('cancel')">
        <div class="confirm-card">
            <div class="confirm-header" :class="{ 'confirm-header--danger': danger }">
                <span class="confirm-icon">{{ danger ? '⚠' : 'ℹ' }}</span>
                <h3>{{ title ?? 'Confirmar acción' }}</h3>
            </div>
            <div class="confirm-body">
                <p>{{ message }}</p>
            </div>
            <div class="confirm-actions">
                <button class="confirm-btn-cancel" @click="emit('cancel')">
                    {{ cancelLabel ?? 'Cancelar' }}
                </button>
                <button class="confirm-btn-confirm" :class="{ 'confirm-btn-confirm--danger': danger }" @click="emit('confirm')">
                    {{ confirmLabel ?? 'Confirmar' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import '@/assets/styles/shared/confirmModal.css';
</style>
