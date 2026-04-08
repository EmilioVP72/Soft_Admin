<script setup lang="ts">
import {
    ExclamationTriangleIcon,
    CheckCircleIcon,
    XCircleIcon,
    InformationCircleIcon,
    QuestionMarkCircleIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

export type ModalType = 'confirm' | 'info' | 'warning' | 'error' | 'success';

interface Props {
    isOpen: boolean;
    type?: ModalType;
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    isDangerous?: boolean; // Para acciones destructivas
}

const props = withDefaults(defineProps<Props>(), {
    type: 'confirm',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    isDangerous: false
});

const emit = defineEmits<{
    confirm: [];
    cancel: [];
    close: [];
}>();

const iconComponent = computed(() => {
    switch (props.type) {
        case 'confirm':
            return QuestionMarkCircleIcon;
        case 'error':
            return XCircleIcon;
        case 'warning':
            return ExclamationTriangleIcon;
        case 'success':
            return CheckCircleIcon;
        case 'info':
        default:
            return InformationCircleIcon;
    }
});

const modalClass = computed(() => `modal modal-${props.type}`);

const handleConfirm = () => {
    emit('confirm');
    emit('close');
};

const handleCancel = () => {
    emit('cancel');
    emit('close');
};

const handleBackdropClick = (e: MouseEvent) => {
    if (e.target === e.currentTarget) {
        handleCancel();
    }
};
</script>

<template>
    <Teleport to="body" v-if="isOpen">
        <div class="modal-overlay" @click="handleBackdropClick">
            <div :class="modalClass" role="alertdialog" aria-modal="true">
                <div class="modal-icon">
                    <component :is="iconComponent" />
                </div>
                <h2 class="modal-title">{{ title }}</h2>
                <p class="modal-message">{{ message }}</p>
                <div class="modal-actions">
                    <button
                        class="btn btn-secondary"
                        @click="handleCancel"
                    >
                        {{ cancelText }}
                    </button>
                    <button
                        class="btn"
                        :class="isDangerous ? 'btn-danger' : 'btn-primary'"
                        @click="handleConfirm"
                    >
                        {{ confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.2s ease-out;
}

.modal {
    background-color: white;
    border-radius: 8px;
    padding: 32px;
    max-width: 400px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    text-align: center;
    animation: modalSlideIn 0.3s ease-out;
}

.modal-icon {
    width: 48px;
    height: 48px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-confirm .modal-icon {
    color: #2563eb;
}

.modal-error .modal-icon {
    color: #dc2626;
}

.modal-warning .modal-icon {
    color: #ea580c;
}

.modal-success .modal-icon {
    color: #16a34a;
}

.modal-info .modal-icon {
    color: #2563eb;
}

.modal-title {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
}

.modal-message {
    margin: 0 0 24px 0;
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-danger {
    background-color: #dc2626;
    color: white;
}

.btn-danger:hover {
    background-color: #b91c1c;
}

.btn-secondary {
    background-color: #e5e7eb;
    color: #1f2937;
}

.btn-secondary:hover {
    background-color: #d1d5db;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes modalSlideIn {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
