# Routing y Navegación

Configuración de Vue Router y gestión de rutas.

## Configuración

**Ubicación**: `/src/router/index.ts`

```typescript
import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [...]
})
```

## Rutas Definidas

### Login
```typescript
{
  path: '/',
  name: 'login',
  component: () => import('@/views/auth/LoginView.vue'),
  meta: { 
    showNavbar: false,
    requiresAuth: false 
  }
}
```

### Dashboard
```typescript
{
  path: '/dashboard',
  name: 'dashboard',
  component: () => import('@/views/dashboard/DashboardView.vue'),
  meta: { 
    showNavbar: true,
    requiresAuth: true 
  }
}
```

## Meta Fields

| Field | Tipo | Descripción |
|-------|------|-------------|
| showNavbar | boolean | Si mostrar el Navbar |
| requiresAuth | boolean | Si requiere autenticación |

## Navigation Guards

### Global Before Guard

```typescript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if(to.meta.requiresAuth) {
    if (authStore.token) {
      next() // Autenticado, permitir
    } else {
      next({ name: 'login' }) // Redirigir a login
    }
  } else {
    if(to.name === 'login' && authStore.token){
      next({ name: 'dashboard' }) // Ya logueado, ir a dashboard
    } else {
      next()
    }
  }
})
```

## Lazy Loading

Todas las vistas usan lazy loading:
```typescript
component: () => import('@/views/auth/LoginView.vue')
```

**Beneficios**:
- Code splitting automático
- Carga inicial más rápida
- Solo carga la vista cuando se navega a ella

## Navegación Programática

```typescript
import { useRouter } from 'vue-router'

const router = useRouter()

// Push (agrega al historial)
router.push({ name: 'dashboard' })
router.push('/dashboard')

// Replace (reemplaza historial actual)
router.replace({ name: 'login' })

// Go back
router.go(-1)
router.back()
```

## RouterLink en Templates

```vue
<RouterLink to="/dashboard">Dashboard</RouterLink>
<RouterLink :to="{ name: 'dashboard' }">Dashboard</RouterLink>

<!-- Con parámetros -->
<RouterLink :to="{ name: 'user', params: { id: 123 }}">
  Usuario
</RouterLink>
```

## useRoute vs useRouter

```typescript
import { useRoute, useRouter } from 'vue-router'

// useRoute - Leer info de la ruta actual
const route = useRoute()
console.log(route.name)
console.log(route.params)
console.log(route.meta)

// useRouter - Navegar
const router = useRouter()
router.push('/dashboard')
```

## Ejemplo de Rutas Futuras

```typescript
// Productos
{
  path: '/products',
  name: 'products',
  component: () => import('@/views/products/ProductsView.vue'),
  meta: { requiresAuth: true, showNavbar: true }
},
{
  path: '/products/:id',
  name: 'product-detail',
  component: () => import('@/views/products/ProductDetail.vue'),
  meta: { requiresAuth: true, showNavbar: true }
},

// Reportes
{
  path: '/reports',
  name: 'reports',
  component: () => import('@/views/reports/ReportsView.vue'),
  meta: { requiresAuth: true, showNavbar: true }
}
```

## Referencias

- [Vue Router Documentation](https://router.vuejs.org/)
