import apiClient from '@/api/axios';


export default {
    loginUser(credentials: { email: string; password: string }) {
        return apiClient.post('/auth/login', credentials);
    },

    meUser(){
        return apiClient.get('/auth/me');
    },

    logoutUser() {
        return apiClient.post('/auth/logout');
    },

    refreshToken(){
        return apiClient.post('/auth/refresh');
    }
}