# Documentación de Componentes

Este documento describe todos los componentes Vue de la aplicación, sus props, eventos y uso.

## Índice de Componentes

### Componentes Globales
- [App.vue](#appvue)
- [Navbar.vue](#navbarvue)
- [SessionModal.vue](#sessionmodalvue)

### Vistas (Views)
- [LoginView.vue](#loginviewvue)
- [DashboardView.vue](#dashboardviewvue)

### Componentes del Dashboard
- [FastActionDashboard.vue](#fastactiondashboardvue)
- [Report.vue](#reportvue)
- [Graphics.vue](#graphicsvue)
- [BarGraph.vue](#bargraphvue)

---

## App.vue

**Ubicación**: `/src/App.vue`

**Tipo**: Componente raíz de la aplicación

**Descripción**: Contiene la estructura principal de la aplicación con el router-view y componentes globales.

### Template

```vue
<template>
  <header v-if="route.meta.showNavbar">
    <NavBar />
  </header>

  <main>
    <RouterView />

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
```

### Script

```typescript
import { RouterView, useRoute } from 'vue-router'
import NavBar from './components/Navbar.vue'
import SessionModal from './components/SessionModal.vue'
import { useSessionWarning } from './composables/useSessionWarning'

const route = useRoute()
const { showWarningModal, isLoading, refreshToken, logout } = useSessionWarning()
```

### Características

- Muestra `Navbar` condicionalmente según `route.meta.showNavbar`
- Utiliza `Teleport` para renderizar `SessionModal` en el body
- Integra el composable `useSessionWarning` para gestión de sesiones
- Contiene `RouterView` para renderizar vistas dinámicas

### Dependencias

- `vue-router`: useRoute, RouterView
- `useSessionWarning`: Composable para advertencia de sesión
- Navbar.vue
- SessionModal.vue

---

## Navbar.vue

**Ubicación**: `/src/components/Navbar.vue`

**Tipo**: Componente de navegación global

**Descripción**: Barra de navegación con enlaces y botón de logout.

### Props

Ninguna

### Emits

Ninguno

### Data

```typescript
const isMenuOpen = ref(false)
const menuItems = [
  { name: 'Dashboard', path: '/dashboard' },
  // Más items...
]
```

### Métodos

```typescript
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const logout = () => {
  LoginServices.logoutUser().then(() => {
    authStore.logout()
    router.push({ name: 'login' })
  })
}
```

### Template

```vue
<nav class="navbar">
  <section class="menu-container">
    <div class="nav-links" :class="{ 'open': isMenuOpen }">
      <ul>
        <li v-for="item in menuItems" :key="item.path">
          <router-link :to="item.path" @click="isMenuOpen = false">
            {{ item.name }}
          </router-link>
        </li>
        <li>
          <button class="logout-button" @click="logout">
            Cerrar Sesión
          </button>
        </li>
      </ul>
    </div>
    
    <button class="menu-toggle" @click="toggleMenu">
      ☰
    </button>
  </section>
</nav>
```

### Estilos

Importa: `@/assets/styles/base/navbar.css`

### Características

- Menu responsive (toggle para móviles)
- Integración con Vue Router
- Logout que llama al backend antes de limpiar estado local

### Uso

```vue
<NavBar />
```

---

## SessionModal.vue

**Ubicación**: `/src/components/SessionModal.vue`

**Tipo**: Modal de advertencia de sesión

**Descripción**: Modal que alerta al usuario 5 minutos antes de que expire su sesión.

### Props

```typescript
defineProps<{
  isLoading: boolean;
}>()
```

| Prop | Tipo | Requerido | Descripción |
|------|------|-----------|-------------|
| isLoading | boolean | Sí | Indica si se está procesando la renovación |

### Emits

```typescript
const emit = defineEmits<{
  (e: 'confirm'): void;
  (e: 'cancel'): void;
}>()
```

| Evento | Payload | Descripción |
|--------|---------|-------------|
| confirm | void | Usuario quiere mantener la sesión |
| cancel | void | Usuario quiere cerrar sesión |

### Características

- **Accesibilidad**:
  - Roles ARIA (`role="dialog"`, `aria-modal="true"`)
  - Auto-focus al primer botón
  - Restaura focus al cerrar
  - Escape key para cerrar
  - Bloquea scroll del body cuando está abierto

- **UX**:
  - Overlay que cubre toda la pantalla
  - Botones claramente diferenciados
  - Estado de loading en botón de confirmar

### Template

```vue
<div class="modal-overlay" role="dialog" aria-modal="true">
  <div class="modal-content">
    <div class="modal-header">
      <h3>⚠️ Tu sesión está por expirar</h3>
    </div>
    
    <div class="modal-body">
      <p>Por seguridad, tu sesión se cerrará en menos de 5 minutos...</p>
    </div>

    <div class="modal-actions">
      <button @click="emit('cancel')" class="btn btn-secondary">
        Cerrar Sesión
      </button>
      
      <button @click="emit('confirm')" :disabled="isLoading" class="btn btn-primary">
        {{ isLoading ? 'Renovando...' : 'Mantener Sesión' }}
      </button>
    </div>
  </div>
</div>
```

### Lifecycle

```typescript
onMounted(() => {
  previousActiveElement.value = document.activeElement as HTMLElement
  document.body.classList.add('modal-open')
  // Auto-focus
  setTimeout(() => {
    const firstButton = modalRef.value?.querySelector('button')
    firstButton?.focus()
  }, 100)
})

onUnmounted(() => {
  document.body.classList.remove('modal-open')
  previousActiveElement.value?.focus()
})
```

### Estilos

Importa: `@/assets/styles/base/SessionModal.css`

### Uso

```vue
<SessionModal 
  v-if="showWarningModal" 
  :isLoading="isLoading" 
  @confirm="refreshToken" 
  @cancel="logout"
/>
```

---

## LoginView.vue

**Ubicación**: `/src/views/auth/LoginView.vue`

**Tipo**: Vista de autenticación

**Descripción**: Formulario de login con validación.

### Data

```typescript
const formData = reactive({
  email: '',
  password: ''
})
const isLoading = ref(false)
const formErrors = ref<Partial<Record<keyof LoginData, string>>>({})
```

### Validación (Zod)

```typescript
const loginSchema = z.object({
  email: z.string()
    .min(1, "El correo electrónico es obligatorio.")
    .email("El correo electrónico no es válido."),
  password: z.string()
    .min(6, "La contraseña debe tener al menos 6 caracteres.")
})
```

### Métodos

```typescript
const validate = (): boolean => {
  formErrors.value = {}
  const result = loginSchema.safeParse(formData)
  
  if(!result.success){
    result.error.issues.forEach((error) => {
      const field = error.path[0] as keyof LoginData
      formErrors.value[field] = error.message
    })
  }
  return result.success
}

const submitForm = async () => {
  isLoading.value = true
  if(!validate()) return
  
  try {
    const response = await LoginServices.loginUser(formData)
    authStore.setAuthData(response.data.data.token, response.data.data.expires_in)
    router.push({ name: 'dashboard' })
  } catch (error) {
    // Manejo de errores
  } finally {
    isLoading.value = false
  }
}
```

### Template

```vue
<div class="login-wrapper">
  <div class="login-container">
    <h1>Bienvenido(a)</h1>
    <h2>Iniciar Sesión</h2>

    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input
          id="email"
          type="email"
          v-model="formData.email"
          :class="{'input-error': formErrors.email}"
          :disabled="isLoading"
        />
        <span v-if="formErrors.email" class="error-message">
          {{ formErrors.email }}
        </span>
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input
          id="password"
          type="password"
          v-model="formData.password"
          :class="{'input-error': formErrors.password}"
          :disabled="isLoading"
        />
        <span v-if="formErrors.password" class="error-message">
          {{ formErrors.password }}
        </span>
      </div>

      <button type="submit" :disabled="isLoading">
        {{ isLoading ? "Cargando..." : "Iniciar Sesión" }}
      </button>
    </form>
  </div>
</div>
```

### Características

- Validación client-side con Zod
- Mensajes de error específicos por campo
- Deshabilitación de inputs durante loading
- Manejo de errores 401 y 422 del backend
- Auto-redirección al dashboard tras login exitoso

### Estilos

Importa: `@/assets/styles/auth/login/login.css`

---

## DashboardView.vue

**Ubicación**: `/src/views/dashboard/DashboardView.vue`

**Tipo**: Vista principal del dashboard

**Descripción**: Contenedor principal del dashboard con bienvenida y componentes.

### Data

```typescript
const user_name = ref('')
```

### Lifecycle

```typescript
onMounted(async () => {
  try {
    const response = await LoginServices.meUser()
    if(response.data.data.user === null){
      user_name.value = "Usuario"
    } else {
      user_name.value = response.data.data.user
    }
  } catch (error) {
    console.error(error)
  }
})
```

### Template

```vue
<div class="dashboard-view">
  <h1>Bienvenido(a) {{ user_name }} al Panel de Control</h1>
  <section class="main-dashboard">
    <FastActionDashboard />
    <Report />
    <Graphics />
  </section>
</div>
```

### Componentes Hijos

- FastActionDashboard
- Report
- Graphics

### Estilos

Importa: `@/assets/styles/dashboard/dashboard.css`

---

## FastActionDashboard.vue

**Ubicación**: `/src/components/dashboard/FastActionDashboard.vue`

**Tipo**: Componente de acciones rápidas

**Descripción**: Tarjetas con acciones rápidas comunes.

### Template

```vue
<div class="fast-action-dashboard">
  <section class="actions-container">
    <h2>Acciones Rápidas</h2>
    <section class="actions">
      <section class="action">
        <DocumentCurrencyDollarIcon class="icon"/>
        <h3>Realizar Factura</h3>
        <p>Para crear una factura ingresando los datos necesarios</p>
        <RouterLink to="" class="btn-action">
          Crear Factura
        </RouterLink>
      </section>
      
      <section class="action">
        <BanknotesIcon class="icon"/>
        <h3>Verificar Promociones</h3>
        <p>Calcular el total de ventas de un proveedor...</p>
        <RouterLink to="" class="btn-action">
          Calcular Promociones
        </RouterLink>
      </section>
      
      <section class="action">
        <TableCellsIcon class="icon"/>
        <h3>Datos del Negocio</h3>
        <p>Visualizar datos sobre sucursales, empleados...</p>
        <RouterLink to="" class="btn-action">
          Ver Datos
        </RouterLink>
      </section>
    </section>
  </section>
</div>
```

### Iconos Utilizados

- `DocumentCurrencyDollarIcon`
- `BanknotesIcon`
- `TableCellsIcon`

### Estilos

Importa: `@/assets/styles/dashboard/componentes/fastActionsDashboard.css`

### Nota

Los enlaces actualmente apuntan a "" (pendiente de implementar las rutas).

---

## Report.vue

**Ubicación**: `/src/components/dashboard/Report.vue`

**Tipo**: Componente de reportes

**Descripción**: Tarjetas para generar reportes en PDF o Excel.

### Template

```vue
<div class="reports-dashboard">
  <section class="reports-container">
    <h2>Reportes Rapidos</h2>
    <section class="reports">
      <section class="report">
        <PresentationChartLineIcon class="icon"/>
        <h3>Reporte de Ventas General</h3>
        <p>Genera un reporte en PDF o Excel...</p>
        <div class="report-actions">
          <button class="btn-report">Generar PDF</button>
          <button class="btn-excel">Generar Excel</button>
        </div>
      </section>
      
      <section class="report">
        <BuildingStorefrontIcon class="icon"/>
        <h3>Reporte de Ventas por Tienda</h3>
        <p>Genera un reporte en PDF o Excel...</p>
        <div class="report-actions">
          <button class="btn-report">Generar PDF</button>
          <button class="btn-excel">Generar Excel</button>
        </div>
      </section>
      
      <section class="report">
        <UserCircleIcon class="icon"/>
        <h3>Reporte de Empleados</h3>
        <p>Genera un reporte en PDF o Excel...</p>
        <div class="report-actions">
          <button class="btn-report">Generar PDF</button>
          <button class="btn-excel">Generar Excel</button>
        </div>
      </section>
    </section>
  </section>
</div>
```

### Iconos Utilizados

- `PresentationChartLineIcon`
- `BuildingStorefrontIcon`
- `UserCircleIcon`

### Estilos

Importa: `@/assets/styles/dashboard/componentes/report.css`

### Nota

Los botones actualmente no tienen funcionalidad (pendiente de implementar).

---

## Graphics.vue

**Ubicación**: `/src/components/dashboard/Graphics.vue`

**Tipo**: Componente de gráficas

**Descripción**: Muestra gráficas de ventas general y por sucursal.

### Data

```typescript
const message = ref('')
const isValid = ref(false)

// Para gráfica general
const chartData = ref<ChartData<'bar'>>({ datasets: [] })
const chartOptions = ref<ChartOptions<'bar'>>({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom' },
    title: { display: true, text: 'Ingresos Totales Por Departamento' }
  }
})

// Para gráficas por sucursal
const message2 = ref('')
const isValid2 = ref(false)
const storeCharts = ref<Array<{
  storeId: number
  storeName: string
  chartData: ChartData<'bar'>
  chartOptions: ChartOptions<'bar'>
}>>([])
```

### Lifecycle

```typescript
onMounted(async () => {
  // Cargar gráfica general
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
    isValid.value = true
    message.value = error.response?.data?.message
  }
  
  // Cargar gráficas por sucursal
  try {
    const storesResponse = await StoresServices.getStores()
    const stores = storesResponse.data.data
    
    for (let i = 0; i < stores.length; i++) {
      const store = stores[i]
      const response = await StoresServices.getSalesByDepartmentByStore(store.id)
      // ... crear chartData para cada tienda
      storeCharts.value.push({...})
    }
  } catch (error) {
    // Manejo de errores
  }
})
```

### Template

```vue
<div class="graphics">
  <section class="graphics-stores-departments">
    <section class="general-graphics">
      <h2>Resumen General de Ventas - Todas las Sucursales</h2>
      <p v-if="isValid" class="error-message">{{ message }}</p>
      <BarGraph v-else :chartData="chartData" :chartOptions="chartOptions" />
    </section>

    <section class="graphics-for-stores">
      <h2>Ventas por Departamento según Sucursal</h2>
      <p v-if="isValid2" class="error-message">{{ message2 }}</p>
      <div v-else class="store-charts-container">
        <div v-for="chart in storeCharts" :key="chart.storeId" class="store-chart">
          <BarGraph :chartData="chart.chartData" :chartOptions="chart.chartOptions" />
        </div>
      </div>
    </section>
  </section>
</div>
```

### Características

- Carga dinámica de datos desde API
- Gráfica general con todas las sucursales
- Gráficas individuales por cada sucursal
- Manejo de errores (404 cuando no hay datos)
- Colores diferentes para cada gráfica de sucursal

### Estilos

Importa: `@/assets/styles/dashboard/componentes/graphics.css`

---

## BarGraph.vue

**Ubicación**: `/src/components/dashboard/graphics/BarGraph.vue`

**Tipo**: Componente de gráfica reutilizable

**Descripción**: Wrapper de Chart.js para gráficas de barras.

### Props

```typescript
defineProps<{
  chartData: ChartData<'bar'>
  chartOptions: ChartOptions<'bar'>
}>()
```

| Prop | Tipo | Requerido | Descripción |
|------|------|-----------|-------------|
| chartData | ChartData<'bar'> | Sí | Datos de la gráfica (labels, datasets) |
| chartOptions | ChartOptions<'bar'> | Sí | Opciones de configuración de Chart.js |

### Registro de Chart.js

```typescript
import {
  Chart as ChartJS,
  Title, Tooltip, Legend,
  BarElement,
  CategoryScale, LinearScale
} from 'chart.js'

ChartJS.register(
  Title, Tooltip, Legend,
  BarElement,
  CategoryScale, LinearScale
)
```

### Template

```vue
<div class="chart-container">
  <Bar :data="chartData" :options="chartOptions" />
</div>
```

### Estilos

```css
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
```

### Características

- Responsive
- Altura controlada (300px)
- Reutilizable para múltiples gráficas
- Props tipadas con TypeScript

### Uso

```vue
<BarGraph 
  :chartData="myChartData" 
  :chartOptions="myChartOptions" 
/>
```

---

## Resumen de Componentes

| Componente | Tipo | Props | Emits | Descripción |
|------------|------|-------|-------|-------------|
| App.vue | Root | - | - | Componente raíz |
| Navbar.vue | Layout | - | - | Navegación global |
| SessionModal.vue | Modal | isLoading | confirm, cancel | Modal de advertencia |
| LoginView.vue | View | - | - | Vista de login |
| DashboardView.vue | View | - | - | Vista de dashboard |
| FastActionDashboard.vue | Feature | - | - | Acciones rápidas |
| Report.vue | Feature | - | - | Generación de reportes |
| Graphics.vue | Feature | - | - | Visualización de ventas |
| BarGraph.vue | Presentational | chartData, chartOptions | - | Gráfica de barras |

## Patrones de Componentes

### Presentational vs Container

- **Presentational**: BarGraph (solo presenta datos)
- **Container**: Graphics (maneja lógica y estado)

### Composición

- DashboardView compone FastActionDashboard, Report, Graphics
- Graphics compone múltiples BarGraph

### Reutilización

- BarGraph es completamente reutilizable
- SessionModal es reutilizable

## Mejores Prácticas Aplicadas

✅ Componentes pequeños y enfocados
✅ Props y emits tipados con TypeScript
✅ Separación de concerns (lógica vs presentación)
✅ Uso de composables para lógica reutilizable
✅ Estilos scoped o en archivos separados
✅ Accesibilidad (ARIA labels, roles)
✅ Manejo de estados de loading y error
✅ Validación de datos

## Convenciones de Naming

- **Componentes**: PascalCase.vue
- **Props**: camelCase
- **Emits**: kebab-case en template, camelCase en script
- **CSS classes**: kebab-case
