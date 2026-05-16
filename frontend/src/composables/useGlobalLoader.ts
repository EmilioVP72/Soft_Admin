import { ref } from 'vue';

const isLoading = ref(false);
const progress = ref(0);
let activeRequests = 0;
let progressInterval: number | null = null;

const startLoading = () => {
    activeRequests++;
    if (activeRequests === 1) {
        isLoading.value = true;
        progress.value = 0;
        
        // Simular progreso
        progressInterval = window.setInterval(() => {
            if (progress.value < 85) {
                // Sube aleatoriamente entre 1 y 5%
                progress.value += Math.floor(Math.random() * 5) + 1;
            } else if (progress.value < 95) {
                // Sube muy lento después del 85%
                progress.value += 1;
            }
        }, 300);
    }
};

const stopLoading = () => {
    activeRequests--;
    if (activeRequests <= 0) {
        activeRequests = 0;
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
        
        progress.value = 100;
        
        // Ocultar después de un pequeño retraso para que se vea el 100%
        setTimeout(() => {
            isLoading.value = false;
            progress.value = 0;
        }, 500);
    }
};

const forceStop = () => {
    activeRequests = 0;
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    isLoading.value = false;
    progress.value = 0;
}

export const useGlobalLoader = () => {
    return {
        isLoading,
        progress,
        startLoading,
        stopLoading,
        forceStop
    };
};
