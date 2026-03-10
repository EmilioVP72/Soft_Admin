<script setup lang="ts">
import { 
    ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

defineProps<{
    tittle: string;
    message: string;
}>();
</script>

<template>
    <section class="error-container">
        <h1>{{ tittle}}</h1>
        <ExclamationTriangleIcon class="error-icon"/>
        <p>{{ message}}</p>
    </section>
</template>

<style src="@/assets/styles/shared/error.css" scoped>  </style>