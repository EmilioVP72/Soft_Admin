<script setup lang="ts">

import { ref} from 'vue'
import { RouterView, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth';
import LoginServices from '@/services/LoginServices';

const router = useRouter();
const authStore = useAuthStore();
const isMenuOpen = ref(false);

const menuItems = [
  { name: 'Inicio', path: '/dashboard' },
  { name: 'Datos', path: '/data' },
  { name: 'Calculos', path: '/calculate' },
  
  // Agrega más elementos del menú según sea necesario
];

const toggleMenu = () => {  isMenuOpen.value = !isMenuOpen.value; };

const logout = () => {
    
    LoginServices.logoutUser().then(() => {
        authStore.logout();
        router.push({ name: 'login' });
    }).catch((error) => {
        console.error('Error during logout:', error);
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