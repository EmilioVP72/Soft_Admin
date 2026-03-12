<script setup lang="ts">
import Graphics from '@/components/dashboard/Graphics.vue';
import LoginServices from '@/services/LoginServices';
import { onMounted, ref } from 'vue';
import FastActionDashboard from '@/components/dashboard/FastActionDashboard.vue';
import Report from '@/components/dashboard/Report.vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import Error from '@/components/shared/Error.vue';



const user_name = ref('');
const error_data = ref<boolean>(false);

onMounted(async () => {
    try {
        const response = await LoginServices.meUser();
        if(response.data.data.user === null){
            user_name.value = "Usuario";
        }else{
            user_name.value = response.data.data.user;
        }
        error_data.value = false;
    } catch (error) {
        error_data.value = true;
            // Cerrar sesión y redirigir al login
        setTimeout(() => {
            const authStore = useAuthStore();
            const router = useRouter();
            authStore.logout();
            router.push('/login');
        }, 5000); 
    }
});

</script>

<template>
    <div class="dashboard-view">
        <Error v-if="error_data" 
        tittle="Error al cargar los datos del panel de control"
        message="Hubo un error al cargar los datos. Por favor, inténtalo de nuevo más tarde o contacta al soporte si el problema persiste. Se cerrará tu sesión por seguridad."
        />
        <section v-else="error_data" class="dashboard-content">
            <h1>Bienvenido(a) {{ user_name }} al Panel de Control</h1>
            <section class="main-dashboard">
                <FastActionDashboard />
                <Report />
                <Graphics />
            </section>
        </section>
    </div>
</template>

<style src="@/assets/styles/dashboard/dashboard.css"></style>