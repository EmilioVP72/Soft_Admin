<script setup lang="ts">
import { useNotification } from '@/composables/useNotification';
import Toast from '@/components/shared/Toast.vue';
import Modal from '@/components/shared/Modal.vue';

const { toasts, removeToast, modal, handleModalConfirm, handleModalCancel } = useNotification();
</script>

<template>
    <!-- Contenedor de Toasts -->
    <div class="toasts-container">
        <Toast
            v-for="toast in toasts"
            :key="toast.id"
            :type="toast.type"
            :title="toast.title"
            :message="toast.message"
            :duration="toast.duration"
            @close="removeToast(toast.id)"
        />
    </div>

    <!-- Modal de Confirmación -->
    <Modal
        :is-open="modal.isOpen"
        :type="modal.type"
        :title="modal.title"
        :message="modal.message"
        :confirm-text="modal.confirmText"
        :cancel-text="modal.cancelText"
        :is-dangerous="modal.isDangerous"
        @confirm="handleModalConfirm"
        @cancel="handleModalCancel"
        @close="handleModalCancel"
    />
</template>

<style scoped>
.toasts-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 500px;
    z-index: 999;
    pointer-events: none;
}

.toasts-container :deep(> *) {
    pointer-events: auto;
}
</style>
