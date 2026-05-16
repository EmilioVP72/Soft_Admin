<script setup lang="ts">
import { RouterView, useRoute } from 'vue-router'
import NavBar from './components/Navbar.vue'
import SessionModal from './components/SessionModal.vue'
import NotificationCenter from './components/NotificationCenter.vue'
import GlobalLoader from './components/shared/GlobalLoader.vue'
import { useSessionWarning } from './composables/useSessionWarning'

const route = useRoute()
const { showWarningModal, isLoading, refreshToken, logout } = useSessionWarning()

</script>

<template>
<header v-if="route.meta.showNavbar">
    <NavBar />
  </header>

  <main>
    <GlobalLoader />
    <RouterView />

    <NotificationCenter />

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
