<script setup lang="ts">
import Graphics from '@/components/dashboard/Graphics.vue';
import LoginServices from '@/services/LoginServices';
import { onMounted, ref } from 'vue';
import FastActionDashboard from '@/components/dashboard/FastActionDashboard.vue';
import Report from '@/components/dashboard/Report.vue';


const user_name = ref('');

onMounted(async () => {
    try {
        const response = await LoginServices.meUser();
        if(response.data.data.user === null){
            user_name.value = "Usuario";
        }else{
            user_name.value = response.data.data.user;
        }
    } catch (error) {
        console.error(error);
    }
});

</script>

<template>
    <div class="dashboard-view">
        <h1>Bienvenido(a) {{ user_name }} al Panel de Control</h1>
        <section class="main-dashboard">
            <FastActionDashboard />
            <Report />
            <Graphics />
        </section>
    </div>
</template>

<style src="@/assets/styles/dashboard/dashboard.css"></style>