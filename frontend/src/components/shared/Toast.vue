<script setup lang="ts">
import { 
    ExclamationTriangleIcon,
    CheckCircleIcon,
    InformationCircleIcon,
    XCircleIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

export type ToastType = 'error' | 'success' | 'warning' | 'info';

interface Props {
    type: ToastType;
    title: string;
    message: string;
    duration?: number; // milisegundos, 0 = no auto-close
}

const props = withDefaults(defineProps<Props>(), {
    duration: 5000
});

const emit = defineEmits<{
    close: [];
}>();

const iconComponent = computed(() => {
    switch (props.type) {
        case 'error':
            return XCircleIcon;
        case 'success':
            return CheckCircleIcon;
        case 'warning':
            return ExclamationTriangleIcon;
        case 'info':
            return InformationCircleIcon;
        default:
            return InformationCircleIcon;
    }
});

const toastClass = computed(() => `toast toast-${props.type}`);

if (props.duration > 0) {
    setTimeout(() => {
        emit('close');
    }, props.duration);
}
</script>

<template>
    <transition name="toast-fade">
        <div :class="toastClass" role="alert">
            <div class="toast-icon">
                <component :is="iconComponent" />
            </div>
            <div class="toast-content">
                <h3 class="toast-title">{{ title }}</h3>
                <p class="toast-message">{{ message }}</p>
            </div>
            <button class="toast-close" @click="$emit('close')" aria-label="Cerrar notificación">
                ✕
            </button>
        </div>
    </transition>
</template>

<style scoped>
.toast {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    background-color: #fff;
    border-left: 4px solid;
    max-width: 500px;
    animation: slideIn 0.3s ease-out;
}

.toast-error {
    border-left-color: #dc2626;
    background-color: #fee2e2;
}

.toast-error .toast-icon {
    color: #dc2626;
}

.toast-success {
    border-left-color: #16a34a;
    background-color: #dcfce7;
}

.toast-success .toast-icon {
    color: #16a34a;
}

.toast-warning {
    border-left-color: #ea580c;
    background-color: #ffedd5;
}

.toast-warning .toast-icon {
    color: #ea580c;
}

.toast-info {
    border-left-color: #2563eb;
    background-color: #dbeafe;
}

.toast-info .toast-icon {
    color: #2563eb;
}

.toast-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-content {
    flex: 1;
    min-width: 0;
}

.toast-title {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.toast-message {
    margin: 0;
    font-size: 13px;
    opacity: 0.9;
}

.toast-error .toast-title {
    color: #7f1d1d;
}

.toast-error .toast-message {
    color: #7f1d1d;
}

.toast-success .toast-title {
    color: #166534;
}

.toast-success .toast-message {
    color: #166534;
}

.toast-warning .toast-title {
    color: #92400e;
}

.toast-warning .toast-message {
    color: #92400e;
}

.toast-info .toast-title {
    color: #1e40af;
}

.toast-info .toast-message {
    color: #1e40af;
}

.toast-close {
    flex-shrink: 0;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
    padding: 4px;
    line-height: 1;
}

.toast-close:hover {
    opacity: 1;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-fade-enter-active,
.toast-fade-leave-active {
    transition: all 0.3s ease;
}

.toast-fade-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-fade-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>
