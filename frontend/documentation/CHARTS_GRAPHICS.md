# Gráficas con Chart.js

Implementación de visualización de datos con Chart.js y vue-chartjs.

## Dependencias

```json
{
  "chart.js": "^4.5.1",
  "vue-chartjs": "^5.3.3"
}
```

## Componente BarGraph

**Ubicación**: `/src/components/dashboard/graphics/BarGraph.vue`

### Configuración de Chart.js

```typescript
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
)
```

### Props

```typescript
defineProps<{
    chartData: ChartData<'bar'>
    chartOptions: ChartOptions<'bar'>
}>()
```

### Template

```vue
<template>
  <div class="chart-container">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>

<style scoped>
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
</style>
```

## Uso en Graphics.vue

### Data Setup

```typescript
import type { ChartData, ChartOptions } from 'chart.js'

const chartData = ref<ChartData<'bar'>>({
  datasets: []
})

const chartOptions = ref<ChartOptions<'bar'>>({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' },
    title: { 
      display: true, 
      text: 'Ingresos Totales Por Departamento' 
    }
  }
})
```

### Cargar Datos desde API

```typescript
onMounted(async () => {
  try {
    const response = await StoresServices.getSalesByDepartment()
    
    const departments = response.data.data.map(item => item.department)
    const sales = response.data.data.map(item => item.totalSales)
    
    chartData.value = {
      labels: departments,
      datasets: [{
        label: 'Ingresos Totales',
        data: sales,
        backgroundColor: 'rgba(75, 192, 192, 0.5)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    }
  } catch (error) {
    // Manejo de errores
  }
})
```

### Múltiples Gráficas

```typescript
const storeCharts = ref<Array<{
  storeId: number
  storeName: string
  chartData: ChartData<'bar'>
  chartOptions: ChartOptions<'bar'>
}>>([])

// Crear gráfica por cada tienda
for (let i = 0; i < stores.length; i++) {
  const store = stores[i]
  const response = await StoresServices.getSalesByDepartmentByStore(store.id)
  
  storeCharts.value.push({
    storeId: store.id,
    storeName: store.name,
    chartData: {
      labels: departments,
      datasets: [{
        label: 'Ingresos Totales',
        data: sales,
        backgroundColor: colors[i].bg,
        borderColor: colors[i].border,
        borderWidth: 1
      }]
    },
    chartOptions: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' },
        title: { 
          display: true, 
          text: `Ventas por Departamento - ${store.name}` 
        }
      }
    }
  })
}
```

### Render Múltiples Gráficas

```vue
<div class="store-charts-container">
  <div v-for="chart in storeCharts" :key="chart.storeId" class="store-chart">
    <BarGraph 
      :chartData="chart.chartData" 
      :chartOptions="chart.chartOptions" 
    />
  </div>
</div>
```

## Configuración de Colores

```typescript
const colors = [
    { bg: 'rgba(255, 99, 132, 0.5)', border: 'rgba(255, 99, 132, 1)' },
    { bg: 'rgba(54, 162, 235, 0.5)', border: 'rgba(54, 162, 235, 1)' },
    { bg: 'rgba(255, 206, 86, 0.5)', border: 'rgba(255, 206, 86, 1)' },
    { bg: 'rgba(75, 192, 192, 0.5)', border: 'rgba(75, 192, 192, 1)' },
    { bg: 'rgba(153, 102, 255, 0.5)', border: 'rgba(153, 102, 255, 1)' },
    { bg: 'rgba(255, 159, 64, 0.5)', border: 'rgba(255, 159, 64, 1)' }
]

const colorIndex = i % colors.length
backgroundColor: colors[colorIndex].bg
borderColor: colors[colorIndex].border
```

## Chart.js Options

### Responsive

```typescript
responsive: true,
maintainAspectRatio: false
```

### Plugins

```typescript
plugins: {
  legend: { 
    position: 'bottom' // 'top' | 'left' | 'right' | 'bottom'
  },
  title: { 
    display: true, 
    text: 'Mi Gráfica' 
  },
  tooltip: {
    enabled: true
  }
}
```

### Scales (Futuro)

```typescript
scales: {
  y: {
    beginAtZero: true,
    ticks: {
      callback: function(value) {
        return '$' + value
      }
    }
  }
}
```

## Tipos de Gráficas

Actualmente: **Bar charts**

Futuro posible:
- Line charts
- Pie charts
- Doughnut charts
- Radar charts

## Performance

✅ Renderizado optimizado con Vue 3
✅ Re-renderiza solo cuando props cambian
✅ `maintainAspectRatio: false` para control preciso

## Manejo de Errores

```typescript
const message = ref('')
const isValid = ref(false)

try {
  // Cargar datos
} catch (error) {
  isValid.value = true
  if(axios.isAxiosError(error)){
    if(error.response?.status === 404){
      message.value = error.response?.data?.message
    }
  }
}
```

```vue
<p v-if="isValid" class="error-message">{{ message }}</p>
<BarGraph v-else :chartData="chartData" :chartOptions="chartOptions" />
```

## Referencias

- [Chart.js Documentation](https://www.chartjs.org/)
- [vue-chartjs Documentation](https://vue-chartjs.org/)
