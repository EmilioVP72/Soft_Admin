import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useAuthStore = defineStore('auth', () =>{
    const token = ref<string | null>(localStorage.getItem('token'));

    
    function setAuthData(newToken: string, expiresIn: number){ 
        token.value = newToken;
        localStorage.setItem('token', newToken);

        const expirationTime = Date.now() + (expiresIn * 1000);
        localStorage.setItem('tokenExpiration', expirationTime.toString());
        
    }

    function logout(){
        
        token.value = null;
        localStorage.clear();
    }

    return { token, setAuthData, logout}

})