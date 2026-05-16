import axios from 'axios';
import router from '../router';
import { useAuthStore } from '../stores/auth';
import { useNotification } from '@/composables/useNotification';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    }
});

apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore();
            const { showError } = useNotification();
            authStore.logout();
            showError('Sesión Expirada', 'Tu sesión ha caducado o no tienes permisos. Por favor, inicia sesión de nuevo.');
            router.replace({ name: 'login' });
        }
        return Promise.reject(error);
    }
);


export default apiClient;