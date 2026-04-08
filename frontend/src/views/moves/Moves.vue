<script setup lang="ts">
import Inputs from '@/components/moves/inputs.vue';
import Outputs from '@/components/moves/Outputs.vue';
import { ref } from 'vue';

const showInputs = ref(true);
const showOutputs = ref(false);

const handleShowInputs = () => {
    showInputs.value = true;
    showOutputs.value = false;
}

const handleShowOutputs = () => {
    showInputs.value = false;
    showOutputs.value = true;
}

</script>

<template>
    <section class="data-view">
        <div class="toolbar">
            <h1>Movimientos</h1>
            <div class="toolbar-actions">
                <button class="btn-tab" :class="{ active: showInputs }" @click="handleShowInputs">Entradas</button>
                <button class="btn-tab" :class="{ active: showOutputs }" @click="handleShowOutputs">Salidas</button>
            </div>
        </div>

        <section class="body-moves">
            <Inputs v-if="showInputs" />
            <Outputs v-if="showOutputs" />
        </section>
    </section>
</template>

<style scoped>
@import '@/assets/styles/moves/moves.css';
</style>