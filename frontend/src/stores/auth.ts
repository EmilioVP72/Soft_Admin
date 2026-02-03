import {defineStore} from 'pinia';
import {ref} from 'vue';
import type {LoginResponse} from '@/interfaces/AuthInterfaces';

export const useAuthStore = defineStore('auth', () =>{
    const token = ref<string | null>(localStorage.getItem('token'));
    const user = ref<LoginResponse['data']['user'] | null>(
        localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user') as string) : null
    );

    function setAuthData(newToken: string, newUser: LoginResponse['data']['user']) {
        token.value = newToken;
        user.value = newUser;
        localStorage.setItem('token', newToken);
        localStorage.setItem('user', JSON.stringify(newUser));
    }

    function logout(){
        token.value = null;
        user.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
    }

    return { token, user, setAuthData, logout}
})