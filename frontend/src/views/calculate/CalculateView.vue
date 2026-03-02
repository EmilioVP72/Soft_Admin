<script setup lang="ts">
import {ref, computed} from 'vue';
import Promotions from '@/components/calculate/Promotions.vue';


const componentsMap = {
    'promociones': Promotions
};

const options = [
    { value: 'promociones', label: 'Promociones' },

];

const selectedOption = ref('promociones');

const activeComponent = computed(() => {
    return componentsMap[selectedOption.value] || null;
});

</script>

<template>
    
    

    <div class="calculate-view">
        <section class="view-header">
            <h1>Área de Cálculo</h1>
            <p>En esta vista se pueden realizar los cálculos de las promociones y otros cálculos relacionados con la administración de las sucursales.</p>
        </section>
        <section class="component-section">
            <label class="component-label">{{ activeComponent?.label }}</label>
            <select
            class="component-select" 
            v-model="selectedOption">
                <option v-for="option in options" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
        </section>
        <section class="component-view">
           <KeepAlive class="component-container">
                <component :is="activeComponent" />
           </KeepAlive>
        </section>
    </div>

</template>

<style src="@/assets/styles/calculate/calculateview.css" scoped>
</style>