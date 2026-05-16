import { ref, computed } from 'vue';
import type { ToastType } from '@/components/shared/Toast.vue';
import type { ModalType } from '@/components/shared/Modal.vue';

interface Toast {
    id: string;
    type: ToastType;
    title: string;
    message: string;
    duration?: number;
}

interface ModalState {
    isOpen: boolean;
    type: ModalType;
    title: string;
    message: string;
    confirmText: string;
    cancelText: string;
    isDangerous: boolean;
    onConfirm?: () => void;
    onCancel?: () => void;
}

const toasts = ref<Toast[]>([]);
const modal = ref<ModalState>({
    isOpen: false,
    type: 'confirm',
    title: '',
    message: '',
    confirmText: 'Confirmar',
    cancelText: 'Cancelar',
    isDangerous: false
});

let toastIdCounter = 0;

export const useNotification = () => {
    const addToast = (type: ToastType, title: string, message: string, duration?: number) => {
        const id = `toast-${++toastIdCounter}`;
        toasts.value.push({
            id,
            type,
            title,
            message,
            duration: duration !== undefined ? duration : (type === 'error' ? 6000 : 4000)
        });

        return id;
    };

    const removeToast = (id: string) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    const showError = (title: string, message: string, duration?: number) => {
        return addToast('error', title, message, duration);
    };

    const showSuccess = (title: string, message: string, duration?: number) => {
        return addToast('success', title, message, duration || 4000);
    };

    const showWarning = (title: string, message: string, duration?: number) => {
        return addToast('warning', title, message, duration || 5000);
    };

    const showInfo = (title: string, message: string, duration?: number) => {
        return addToast('info', title, message, duration || 4000);
    };

    const clearAllToasts = () => {
        toasts.value = [];
    };

    const handleApiError = (error: any) => {
        showError(
            'Atención', 
            'No se pudo procesar la solicitud. Por favor, inténtalo más tarde o contacta a soporte técnico si el problema persiste.',
            6000
        );
    };

    const showConfirmation = (
        title: string,
        message: string,
        onConfirm: () => void,
        options?: {
            confirmText?: string;
            cancelText?: string;
            isDangerous?: boolean;
            type?: ModalType;
        }
    ) => {
        modal.value = {
            isOpen: true,
            type: options?.type || 'confirm',
            title,
            message,
            confirmText: options?.confirmText || 'Confirmar',
            cancelText: options?.cancelText || 'Cancelar',
            isDangerous: options?.isDangerous || false,
            onConfirm
        };
    };

    const closeModal = () => {
        modal.value.isOpen = false;
        modal.value.onConfirm = undefined;
        modal.value.onCancel = undefined;
    };

    const handleModalConfirm = () => {
        modal.value.onConfirm?.();
        closeModal();
    };

    const handleModalCancel = () => {
        modal.value.onCancel?.();
        closeModal();
    };

    return {
        // Toast management
        toasts: computed(() => toasts.value),
        addToast,
        removeToast,
        showError,
        showSuccess,
        showWarning,
        showInfo,
        clearAllToasts,
        handleApiError,

        // Modal management
        modal: computed(() => modal.value),
        showConfirmation,
        closeModal,
        handleModalConfirm,
        handleModalCancel
    };
};

export type UseNotification = ReturnType<typeof useNotification>;
