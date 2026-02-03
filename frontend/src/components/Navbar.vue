<script setup lang="ts">

import { ref} from 'vue'
import { RouterView, useRouter } from 'vue-router'

const router = useRouter();
const isMenuOpen = ref(false);

const menuItems = [
  { name: 'Dashboard', path: '/dashboard' },
  // Agrega más elementos del menú según sea necesario
];

const toggleMenu = () => {  isMenuOpen.value = !isMenuOpen.value; };

const logout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  router.push({ name: 'login' });
};

</script>

<template>

    <nav class="navbar">
        <section class="menu-container">
            <button class="menu-toggle" @click="toggleMenu">
                ☰
            </button>

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
        </section>
    </nav>

</template>

<style src="@/assets/css/views/base/navbar.css"></style>