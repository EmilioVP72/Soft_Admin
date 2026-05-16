<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useNotification } from '@/composables/useNotification';
import { useAuthStore } from '@/stores/auth';
import LoginServices from '@/services/LoginServices';

const router = useRouter();
const { showError } = useNotification();
const authStore = useAuthStore();
const isMenuOpen = ref(false);

const menuItems = [
  { name: 'Inicio', path: '/dashboard' },
  { name: 'Datos', path: '/data' },
  { name: 'Calculos', path: '/calculate' },
  { name: 'Movimientos', path: '/moves' },
  { name: 'Inventario', path: '/inventory' }
];

const toggleMenu = () => {  isMenuOpen.value = !isMenuOpen.value; };

const logout = () => {
    
    LoginServices.logoutUser().then(() => {
        authStore.logout();
        router.push({ name: 'login' });
    }).catch(() => {
        showError('Error', 'No se pudo cerrar la sesión correctamente.');
    });
    
    
};

</script>

<template>

    <nav class="navbar">
        <section class="menu-container">
            

            <div class="nav-links" :class="{ 'open': isMenuOpen }">
                <ul>
                    <li v-for="item in menuItems" :key="item.path">
                        <router-link :to="item.path" class="link" @click="isMenuOpen = false">
                            {{ item.name }}
                        </router-link>

                    </li>
                    <li>
                        <button class="logout-button" @click="logout">
                            Cerrar Sesión
                        </button>
                    </li>
                </ul>
            </div>

            <button class="menu-toggle" @click="toggleMenu">
                ☰
            </button>
        </section>
    </nav>

</template>

<style src="@/assets/styles/base/navbar.css"></style>