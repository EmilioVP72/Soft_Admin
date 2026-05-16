<script setup lang="ts">
import { useGlobalLoader } from '@/composables/useGlobalLoader';

const { isLoading, progress } = useGlobalLoader();
</script>

<template>
  <Transition name="fade">
    <div v-if="isLoading" class="global-loader-overlay">
      <div class="loader-container">
        <div class="svg-wrapper">
          <!-- SVG background (empty/gray) -->
          <svg class="store-svg empty" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
          </svg>

          <!-- SVG filled (colored, clipped by height) -->
          <svg class="store-svg filled" :style="{ clipPath: `inset(${100 - progress}% 0 0 0)` }" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
          </svg>
        </div>
        <div class="progress-text">
          <span>Cargando...</span>
          <span class="percent">{{ progress }}%</span>
        </div>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.global-loader-overlay {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background-color: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: center;
  align-items: center;
}

.loader-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.svg-wrapper {
  position: relative;
  width: 120px;
  height: 120px;
}

.store-svg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.store-svg.empty {
  color: #e2e8f0;
}

.store-svg.filled {
  color: var(--color-accent, #1179a2); /* Use global variable if present */
  transition: clip-path 0.3s ease-out;
}

.progress-text {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: 'Inter', sans-serif;
  color: #334155;
  font-weight: 600;
}

.percent {
  font-size: 1.5rem;
  color: var(--color-accent, #1179a2);
  margin-top: 0.25rem;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
