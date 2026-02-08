import {ref, onMounted, onUnmounted} from 'vue';
import {useAuthStore} from '@/stores/auth';
import LoginServices from '@/services/LoginServices';
import router from '@/router/index';

export function useSessionMonitor(){
    const authStore = useAuthStore();

    const showWarningModal = ref(false);
    const isLoading = ref(false);
    let timer: number| undefined;

    const checkSession = () => {
        const expirationString = localStorage.getItem('tokenExpiration');

        if(!expirationString || !authStore.token) return;

        const expirationTime = parseInt(expirationString);
        const currentTime = Date.now();
        const timeLeft = expirationTime - currentTime;

        // Si ya expiró, cerrar sesión automáticamente
        if(timeLeft <= 0){
            handleLogout();
            return;
        }

        // Mostrar modal 5 minutos (300000ms) antes de expirar
        if(timeLeft <= 300000 && !showWarningModal.value){
            showWarningModal.value = true;
        }
    };

    const refreshToken = async () => {
        isLoading.value = true;
        try {
            const {data} = await LoginServices.refreshToken();
            authStore.setAuthData(data.data.token, data.data.expires_in);
            showWarningModal.value = false;
        } catch (error) {
            console.error('Error refreshing token:', error);
            handleLogout();
        }finally{
            isLoading.value = false;
        }
    };

    const handleLogout = () => {
        authStore.logout();
        router.replace({name: 'login'});
    };

    onMounted(() => {
        timer = setInterval(checkSession, 30000);
    });

    onUnmounted(() => {
        clearInterval(timer);
    });

    return {
        showWarningModal,
        isLoading,
        refreshToken,
        logout: handleLogout
    }
}