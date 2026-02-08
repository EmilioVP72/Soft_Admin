<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import NavBar from './components/Navbar.vue'
import SessionModal from '@/components/SessionModal.vue'
import { useSessionMonitor } from '@/composables/useSessionMonitor'

const { showWarningModal, isLoading, refreshToken, logout } = useSessionMonitor();

const route = useRoute()


</script>

<template>
<header v-if="route.meta.showNavbar">
    <NavBar />
  </header>

  <main>
    <RouterView />

    <Teleport to="body">
      <SessionModal 
        v-if="showWarningModal" 
        :isLoading="isLoading" 
        @confirm="refreshToken" 
        @cancel="logout"
      />
    </Teleport>
  </main>  
</template>
