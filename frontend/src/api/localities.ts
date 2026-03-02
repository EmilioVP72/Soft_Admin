import axios from 'axios';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    }
});

// Seccion: "Interceptor de autenticacion"
// Explicacion: Antes de cada peticion lee el token JWT de localStorage y lo inyecta
//              en el header Authorization; asi el token siempre esta actualizado
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default apiClient;