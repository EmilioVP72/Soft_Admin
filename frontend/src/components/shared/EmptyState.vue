<script setup lang="ts">
defineProps({
    title: {
        type: String,
        default: 'No hay datos'
    },
    message: {
        type: String,
        default: 'No se encontraron registros para mostrar.'
    },
    iconClass: {
        type: String,
        default: 'fas fa-box-open'
    }
});
</script>

<template>
    <div class="empty-state">
        <i :class="iconClass"></i>
        <h3>{{ title }}</h3>
        <p>{{ message }}</p>
    </div>
</template>

<style scoped>
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    background-color: var(--color-bg-card, #ffffff);
    border: 1px dashed var(--color-border, #cbd5e1);
    border-radius: var(--border-radius-lg, 12px);
    color: #64748b;
    text-align: center;
    gap: 1rem;
    margin-top: 1rem;
}

.empty-state i {
    font-size: 3.5rem;
    color: #94a3b8;
    margin-bottom: 0.5rem;
}

.empty-state h3 {
    font-size: 1.25rem;
    color: var(--color-primary, #1e293b);
    margin: 0;
}

.empty-state p {
    font-size: 0.95rem;
    margin: 0;
    max-width: 400px;
}
</style>
