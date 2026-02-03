<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import NavBar from './components/Navbar.vue'

const route = useRoute()

</script>

<template>
<header v-if="route.meta.showNavbar">
    <NavBar />
  </header>

  <main>
    <RouterView />
  </main>  
</template>
