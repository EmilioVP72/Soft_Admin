<script setup>
import { ref, onMounted } from 'vue'

const message = ref('Cargando...')

onMounted(async () => {
  const response = await fetch('http://127.0.0.1:8000/api/test')
  const data = await response.json()
  message.value = data.message
})
</script>

<template>
  <main style="padding: 2rem; font-family: Arial;">
    <h1>Prueba Laravel + Vue 3</h1>
    <p>{{ message }}</p>
  </main>
</template>
