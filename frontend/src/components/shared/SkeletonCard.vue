<script setup lang="ts">
defineProps({
    count: {
        type: Number,
        default: 4
    }
});
</script>

<template>
  <div class="skeleton-grid">
    <div v-for="i in count" :key="i" class="skeleton-card">
      <div class="skeleton skeleton-title"></div>
      <div class="skeleton skeleton-line short"></div>
      <div class="skeleton skeleton-line"></div>
      <div class="skeleton skeleton-box"></div>
      <div class="skeleton-actions">
         <div class="skeleton skeleton-btn"></div>
         <div class="skeleton skeleton-btn"></div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.skeleton-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
  width: 100%;
  padding: 1rem;
}

.skeleton-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Base Skeleton Animation */
.skeleton {
  background: #e2e8f0;
  background: linear-gradient(90deg, #e2e8f0 25%, #cbd5e1 50%, #e2e8f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite linear;
  border-radius: 4px;
}

.skeleton-title {
  height: 28px;
  width: 80%;
  margin-bottom: 0.5rem;
}

.skeleton-line {
  height: 16px;
  width: 100%;
}

.skeleton-line.short {
  width: 60%;
}

.skeleton-box {
  height: 100px;
  width: 100%;
  border-radius: 8px;
  margin-top: 0.5rem;
}

.skeleton-actions {
  display: flex;
  gap: 1rem;
  margin-top: auto;
  padding-top: 1rem;
}

.skeleton-btn {
  height: 36px;
  flex: 1;
  border-radius: 6px;
}

@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
