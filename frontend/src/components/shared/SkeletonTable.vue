<script setup lang="ts">
defineProps({
    rows: {
        type: Number,
        default: 5
    },
    columns: {
        type: Number,
        default: 5
    }
});
</script>

<template>
  <div class="skeleton-table-container">
    <table class="skeleton-table">
      <thead>
        <tr>
          <th v-for="c in columns" :key="'th-'+c">
            <div class="skeleton skeleton-text header-skel"></div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="r in rows" :key="'tr-'+r">
          <td v-for="c in columns" :key="'td-'+r+'-'+c">
            <div class="skeleton skeleton-text"></div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.skeleton-table-container {
  width: 100%;
  overflow-x: auto;
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.skeleton-table {
  width: 100%;
  border-collapse: collapse;
}

.skeleton-table th,
.skeleton-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.skeleton-table th {
  background-color: #f8fafc;
}

/* Base Skeleton Animation */
.skeleton {
  background: #e2e8f0;
  background: linear-gradient(90deg, #e2e8f0 25%, #cbd5e1 50%, #e2e8f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite linear;
  border-radius: 4px;
}

.skeleton-text {
  height: 20px;
  width: 100%;
}

.header-skel {
  height: 24px;
  width: 70%;
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
