# Gestión de Estado con Pinia

Este documento describe cómo se gestiona el estado global usando Pinia.

## Configuración de Pinia

### Instalación y Setup

**main.ts**:
```typescript
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.mount('#app')
```

## Stores Implementados

### Auth Store

**Ubicación**: `/src/stores/auth.ts`

**Propósito**: Gestionar autenticación y tokens JWT

#### State

```typescript
const token = ref<string | null>(localStorage.getItem('token'))
```

| Variable | Tipo | Descripción |
|----------|------|-------------|
| token | string \| null | JWT token del usuario autenticado |

#### Actions

```typescript
function setAuthData(newToken: string, expiresIn: number) { 
    token.value = newToken
    localStorage.setItem('token', newToken)
    
    const expirationTime = Date.now() + (expiresIn * 1000)
    localStorage.setItem('tokenExpiration', expirationTime.toString())
}
```

**Parámetros**:
- `newToken`: JWT recibido del backend
- `expiresIn`: Segundos hasta que expire el token

**Efecto**:
- Actualiza el estado reactivo `token`
- Guarda en localStorage:
  - `token`: El JWT
  - `tokenExpiration`: Timestamp de expiración

```typescript
function logout() {
    token.value = null
    localStorage.clear()
}
```

**Efecto**:
- Limpia el estado
- Elimina todo de localStorage

#### Uso en Componentes

```typescript
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// Leer estado
console.log(authStore.token)

// Ejecutar acciones
authStore.setAuthData(token, expiresIn)
authStore.logout()
```

#### Reactivity

El token es reactivo gracias a `ref()`:
```typescript
<template>
  <div v-if="authStore.token">
    Usuario autenticado
  </div>
</template>
```

### Patrón: Composition API Stores

**Ventajas**:
- Sintaxis similar a Composition API de Vue
- Setup function con `ref()` y `reactive()`
- Return explícito de lo que se expone
- Mejor TypeScript inference

**Estructura**:
```typescript
export const useMyStore = defineStore('myStore', () => {
  // State
  const myState = ref(initialValue)
  
  // Getters (computed)
  const myGetter = computed(() => myState.value * 2)
  
  // Actions
  function myAction(param: string) {
    myState.value = param
  }
  
  return { myState, myGetter, myAction }
})
```

## Persistencia

### LocalStorage Integration

El auth store usa localStorage para persistir datos entre recargas:

```typescript
// Inicialización desde localStorage
const token = ref<string | null>(localStorage.getItem('token'))

// Guardar en localStorage
localStorage.setItem('token', newToken)
localStorage.setItem('tokenExpiration', expirationTime.toString())

// Limpiar localStorage
localStorage.clear()
```

**Datos Persistidos**:
- `token`: JWT string
- `tokenExpiration`: Timestamp en milisegundos

**No Persistido**:
- Datos de API (se re-fetchean)
- UI state temporal

### Plugin de Persistencia (Futuro)

Para auto-persistencia:
```bash
npm install pinia-plugin-persistedstate
```

```typescript
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
```

## Stores Futuros

### User Store

Para información del usuario:
```typescript
export const useUserStore = defineStore('user', () => {
  const user = ref<User | null>(null)
  const permissions = ref<string[]>([])
  
  function setUser(userData: User) {
    user.value = userData
  }
  
  return { user, permissions, setUser }
})
```

### UI Store

Para estado de UI global:
```typescript
export const useUIStore = defineStore('ui', () => {
  const sidebarOpen = ref(false)
  const theme = ref<'light' | 'dark'>('light')
  
  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }
  
  return { sidebarOpen, theme, toggleSidebar }
})
```

### Cart Store (Ejemplo para Ventas)

```typescript
export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([])
  
  const total = computed(() => {
    return items.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
  })
  
  function addItem(item: CartItem) {
    items.value.push(item)
  }
  
  function removeItem(id: number) {
    const index = items.value.findIndex(i => i.id === id)
    if (index > -1) items.value.splice(index, 1)
  }
  
  return { items, total, addItem, removeItem }
})
```

## Best Practices

### ✅ 1. Un Store por Dominio
```typescript
// ✅ Bueno
useAuthStore()
useUserStore()
useProductsStore()

// ❌ Evitar
useGlobalStore() // Demasiado genérico
```

### ✅ 2. Composition API Pattern
```typescript
// ✅ Recomendado
export const useAuthStore = defineStore('auth', () => {
  const token = ref(null)
  function login() { ... }
  return { token, login }
})

// ⚠️ Options API (válido pero menos recomendado)
export const useAuthStore = defineStore('auth', {
  state: () => ({ token: null }),
  actions: { login() { ... } }
})
```

### ✅ 3. TypeScript Types
```typescript
interface User {
  id: number
  email: string
}

const user = ref<User | null>(null)
```

### ✅ 4. Acciones Asíncronas
```typescript
async function fetchUser() {
  isLoading.value = true
  try {
    const response = await api.getUser()
    user.value = response.data
  } catch (error) {
    handleError(error)
  } finally {
    isLoading.value = false
  }
}
```

### ✅ 5. Computed para Getters
```typescript
const isAuthenticated = computed(() => token.value !== null)
const userFullName = computed(() => 
  user.value ? `${user.value.firstName} ${user.value.lastName}` : ''
)
```

## Debugging

### Vue Devtools

Pinia se integra con Vue Devtools:
1. Abrir Vue Devtools
2. Pestaña "Pinia"
3. Ver todos los stores
4. Inspeccionar state
5. Ver acciones ejecutadas
6. Time-travel debugging

### Console Debugging

```typescript
// En componente o store
const authStore = useAuthStore()
console.log('Auth state:', authStore.$state)
console.log('Token:', authStore.token)
```

### Store Reset

```typescript
// Resetear a estado inicial
authStore.$reset()
```

### Subscribe to Changes

```typescript
authStore.$subscribe((mutation, state) => {
  console.log('Store changed:', mutation.type)
  console.log('New state:', state)
})
```

## Testing

### Unit Testing Stores

```typescript
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth'

describe('Auth Store', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })
  
  it('should set token', () => {
    const store = useAuthStore()
    store.setAuthData('token123', 3600)
    expect(store.token).toBe('token123')
  })
  
  it('should logout', () => {
    const store = useAuthStore()
    store.setAuthData('token123', 3600)
    store.logout()
    expect(store.token).toBe(null)
  })
})
```

## Comparación: Pinia vs Vuex

| Característica | Pinia | Vuex |
|---------------|-------|------|
| Sintaxis | Más simple | Más verbosa |
| TypeScript | Excelente | Bueno |
| Mutations | No requeridas | Requeridas |
| Devtools | Sí | Sí |
| Módulos | Stores automáticos | Manual |
| Vue 3 | Oficial | Compatible |

## Arquitectura de Estado

```
┌─────────────────────────────────────┐
│           Components                │
│   (pueden usar múltiples stores)    │
└─────────────┬───────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│         Pinia Stores                │
│  ┌─────────┐  ┌─────────┐           │
│  │  auth   │  │  user   │  ...      │
│  └─────────┘  └─────────┘           │
└─────────────┬───────────────────────┘
              │
              ▼
┌─────────────────────────────────────┐
│        Persistencia                 │
│   (localStorage, API calls)         │
└─────────────────────────────────────┘
```

## Cuándo Usar Store vs Local State

### Usar Store (Global State)
- ✅ Datos compartidos entre múltiples componentes
- ✅ Autenticación
- ✅ Configuración global
- ✅ Datos que persisten entre rutas

### Usar Local State (Component State)
- ✅ UI state específico de un componente
- ✅ Form data temporal
- ✅ Loading/error states locales
- ✅ Datos que no se comparten

## Referencias

- [Pinia Documentation](https://pinia.vuejs.org/)
- [Pinia Composition API](https://pinia.vuejs.org/core-concepts/)
- [Vue Devtools](https://devtools.vuejs.org/)
