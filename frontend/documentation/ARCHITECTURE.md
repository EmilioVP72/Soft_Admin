# Arquitectura del Proyecto Frontend

Este documento describe la arquitectura, patrones de diseño y decisiones técnicas del proyecto.

## Stack Tecnológico

### Core Framework
- **Vue 3.5.24** con Composition API
- **TypeScript 5.9.3** para type safety
- **Vite 7.2.4** como build tool

### Gestión de Estado
- **Pinia 3.0.4** (reemplazo oficial de Vuex para Vue 3)

### Routing
- **Vue Router 4.6.4** con lazy loading

### HTTP Client
- **Axios 1.13.4** con interceptores

### UI/Visualización
- **Chart.js 4.5.1** + **vue-chartjs 5.3.3** para gráficas
- **Heroicons 2.2.0** para iconografía
- **CSS Custom** (no frameworks CSS)

## Arquitectura General

### Patrón Arquitectónico: Feature-Based Modular Architecture

```
┌─────────────────────────────────────────────────────────┐
│                     PRESENTATION LAYER                  │
│  (Views + Components + Composables)                     │
│  - LoginView, DashboardView                             │
│  - Navbar, SessionModal, Graphics                       │
│  - useSessionWarning                                    │
└─────────────────────────────────────────────────────────┘
                         ↓ ↑
┌─────────────────────────────────────────────────────────┐
│                    STATE MANAGEMENT                     │
│  (Pinia Stores)                                         │
│  - auth.ts: token, setAuthData(), logout()              │
└─────────────────────────────────────────────────────────┘
                         ↓ ↑
┌─────────────────────────────────────────────────────────┐
│                     SERVICE LAYER                       │
│  (Services)                                             │
│  - LoginServices: login, logout, refresh, me            │
│  - StoresServices: getSalesByDepartment, getStores      │
└─────────────────────────────────────────────────────────┘
                         ↓ ↑
┌─────────────────────────────────────────────────────────┐
│                    HTTP CLIENT LAYER                    │
│  (API Clients con Axios)                                │
│  - axios.ts: interceptores de auth                      │
│  - stores.ts: cliente para endpoints públicos           │
└─────────────────────────────────────────────────────────┘
                         ↓ ↑
┌─────────────────────────────────────────────────────────┐
│                    BACKEND API REST                     │
│  (Laravel API)                                          │
└─────────────────────────────────────────────────────────┘
```

## Capas de la Aplicación

### 1. Presentation Layer (Capa de Presentación)

**Responsabilidad**: Renderizar UI y manejar interacciones del usuario

**Componentes**:
- **Views**: Componentes de página completa (LoginView, DashboardView)
- **Components**: Componentes reutilizables (Navbar, SessionModal, Graphics)
- **Composables**: Lógica reutilizable (useSessionWarning)

**Patrón**: Composition API de Vue 3

```typescript
// Ejemplo: DashboardView.vue
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import LoginServices from '@/services/LoginServices';

const user_name = ref('');

onMounted(async () => {
  const response = await LoginServices.meUser();
  user_name.value = response.data.data.user;
});
</script>
```

**Características**:
- Reactive state con `ref()` y `reactive()`
- Lifecycle hooks (`onMounted`, `onUnmounted`)
- Template syntax de Vue
- Event handling
- Conditional rendering

### 2. State Management Layer (Gestión de Estado)

**Responsabilidad**: Gestionar estado global compartido entre componentes

**Tecnología**: Pinia (reemplazo oficial de Vuex)

**Stores Implementados**:

#### `auth.ts`
```typescript
export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token'));
  
  function setAuthData(newToken: string, expiresIn: number) {
    token.value = newToken;
    localStorage.setItem('token', newToken);
    localStorage.setItem('tokenExpiration', ...);
  }
  
  function logout() {
    token.value = null;
    localStorage.clear();
  }
  
  return { token, setAuthData, logout }
})
```

**Patrón**: Composition API Stores (setup stores)

**Ventajas sobre Vuex**:
- ✅ Mejor soporte TypeScript
- ✅ No requiere mutaciones
- ✅ Sintaxis más simple
- ✅ Hot reload automático

**Persistencia**: LocalStorage para tokens y tiempos de expiración

### 3. Service Layer (Capa de Servicios)

**Responsabilidad**: Abstraer llamadas a la API y lógica de negocio

**Patrón**: Service Pattern (objeto con métodos estáticos/funciones)

**Ejemplo**:
```typescript
// LoginServices.ts
export default {
  loginUser(credentials: { email: string; password: string }) {
    return apiClient.post('/auth/login', credentials);
  },
  
  meUser() {
    return apiClient.get('/auth/me');
  },
  
  logoutUser() {
    return apiClient.post('/auth/logout');
  },
  
  refreshToken() {
    return apiClient.post('/auth/refresh');
  }
}
```

**Ventajas**:
- Desacopla componentes de la implementación HTTP
- Facilita testing con mocks
- Punto único para cambiar endpoints
- Reutilización de lógica

### 4. HTTP Client Layer (Capa de Cliente HTTP)

**Responsabilidad**: Configurar clientes HTTP con interceptores

**Implementación**: Axios instances

#### `axios.ts` (Cliente Principal)
```typescript
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  headers: { 'Content-Type': 'application/json' }
});

// Interceptor de Request: Agregar token
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }
  return config;
});

// Interceptor de Response: Manejar 401
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore();
      authStore.logout();
      router.replace({ name: 'login' });
    }
    return Promise.reject(error);
  }
);
```

**Patrón**: Axios Interceptors para cross-cutting concerns

**Beneficios**:
- Autenticación automática en todas las peticiones
- Manejo centralizado de errores 401
- Configuración DRY (Don't Repeat Yourself)

## Patrones de Diseño Utilizados

### 1. Composition API Pattern

**Uso**: En todos los componentes y composables

```typescript
// Composable: useSessionWarning.ts
export function useSessionWarning() {
  const showWarningModal = ref(false)
  const isLoading = ref(false)
  
  const refreshToken = async () => { ... }
  const logout = () => { ... }
  
  return {
    showWarningModal,
    isLoading,
    refreshToken,
    logout
  }
}
```

**Ventajas**:
- Lógica reutilizable
- Mejor organización del código
- TypeScript inference automático
- Testeable

### 2. Service Pattern

**Uso**: En la capa de servicios

**Propósito**: Encapsular lógica de negocio y llamadas a API

### 3. Repository Pattern (Parcial)

**Uso**: Los services actúan como repositories

**Abstracción**: Componentes no conocen detalles de HTTP, solo llaman servicios

### 4. Interceptor Pattern

**Uso**: Axios interceptors

**Propósito**: Manejo cross-cutting concerns (auth, logging, errors)

### 5. Observer Pattern

**Uso**: Reactivity system de Vue

**Implementación**: `ref()`, `reactive()`, `watch()`, `computed()`

### 6. Strategy Pattern

**Uso**: Diferentes clientes Axios según necesidad

- `axios.ts`: Con autenticación
- `stores.ts`: Sin autenticación (endpoints públicos)

### 7. Factory Pattern

**Uso**: Creación de instancias de Axios

```typescript
const apiClient = axios.create({ ... });
```

### 8. Guard Pattern

**Uso**: Route guards en Vue Router

```typescript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if(to.meta.requiresAuth) {
    if (authStore.token) next();
    else next({ name: 'login' });
  } else {
    next();
  }
});
```

## Flujo de Datos (Data Flow)

### Flujo de Autenticación

```
1. Usuario ingresa credenciales en LoginView
   ↓
2. LoginView llama LoginServices.loginUser()
   ↓
3. LoginServices hace POST a /auth/login via apiClient
   ↓
4. Backend responde con token y expires_in
   ↓
5. LoginView llama authStore.setAuthData()
   ↓
6. authStore guarda token en state y localStorage
   ↓
7. Router navega a /dashboard
   ↓
8. Router guard verifica authStore.token
   ↓
9. DashboardView se renderiza
```

### Flujo de Petición Autenticada

```
1. Componente llama Service.metodo()
   ↓
2. Service usa apiClient.get/post()
   ↓
3. Interceptor de request agrega Authorization header
   ↓
4. Petición se envía al backend
   ↓
5. Backend valida token
   ↓
6. Si 401: Interceptor de response ejecuta logout
   Si 200: Response se retorna al componente
```

### Flujo de Renovación de Token

```
1. useSessionWarning calcula tiempo de expiración
   ↓
2. 5 minutos antes, muestra SessionModal
   ↓
3. Usuario hace click en "Mantener Sesión"
   ↓
4. SessionModal emite evento @confirm
   ↓
5. useSessionWarning llama LoginServices.refreshToken()
   ↓
6. Backend retorna nuevo token
   ↓
7. authStore.setAuthData() actualiza token
   ↓
8. Modal se cierra, timer se reinicia
```

## Gestión de Estado

### Estado Local (Component State)

Usando `ref()` y `reactive()` en cada componente

**Cuándo usar**:
- Estado que solo usa un componente
- UI state (modales abiertos/cerrados, loading)
- Form data temporal

**Ejemplo**:
```typescript
const isLoading = ref(false);
const formData = reactive({ email: '', password: '' });
```

### Estado Global (Pinia Stores)

**Cuándo usar**:
- Estado compartido entre múltiples componentes
- Autenticación
- Datos del usuario
- Configuración global

**Ejemplo**:
```typescript
const authStore = useAuthStore();
console.log(authStore.token);
```

### Persistencia

**localStorage**:
- Token JWT
- Token expiration time

**No persistido**:
- Datos temporales de API (se re-fetchean)
- UI state

## Routing y Navegación

### Route Structure

```typescript
routes: [
  {
    path: '/',
    name: 'login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { 
      showNavbar: false,
      requiresAuth: false 
    }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/dashboard/DashboardView.vue'),
    meta: { 
      showNavbar: true,
      requiresAuth: true 
    }
  }
]
```

### Navigation Guards

**Global Guard**: `beforeEach` en router/index.ts

**Lógica**:
1. Si ruta requiere auth → verificar token → permitir/redirigir
2. Si ruta es login y ya autenticado → redirigir a dashboard
3. Else → permitir

### Lazy Loading

Todas las vistas se cargan lazy:
```typescript
component: () => import('@/views/auth/LoginView.vue')
```

**Beneficio**: Code splitting, solo se carga la vista cuando se visita

## Manejo de Errores

### Errores de API

```typescript
try {
  const response = await LoginServices.loginUser(credentials);
} catch (error) {
  if(axios.isAxiosError(error)){
    const status = error.response?.status;
    if (status === 401) {
      // Credenciales inválidas
    } else if(status === 422) {
      // Errores de validación
    }
  }
}
```

### Errores Globales

Interceptor de Axios captura 401 → logout automático

### Errores de TypeScript

Type checking en build time con `vue-tsc`

## Validación de Datos

### Validación de Formularios con Zod

```typescript
import {z} from 'zod';

const loginSchema = z.object({
  email: z.string()
    .min(1, "El correo electrónico es obligatorio.")
    .email("El correo electrónico no es válido."),
  password: z.string()
    .min(6, "La contraseña debe tener al menos 6 caracteres.")
})

type LoginData = z.infer<typeof loginSchema>;

const validate = (): boolean => {
  const result = loginSchema.safeParse(formData);
  if(!result.success){
    // Procesar errores
  }
  return result.success;
}
```

**Ventajas**:
- Type safety
- Mensajes de error personalizados
- Schema reusable
- Type inference automático

## Performance Optimizations

### Code Splitting
- Lazy loading de vistas
- Imports dinámicos

### Tree Shaking
- Heroicons: solo se importan iconos usados
- Chart.js: solo plugins registrados

### Reactivity Optimizations
- `ref()` para primitivos
- `reactive()` para objetos
- Computed properties cacheadas

### Build Optimizations
- Vite: bundling optimizado
- Minificación automática
- Asset optimization

## Seguridad

### Authentication
- JWT tokens en localStorage
- Tokens en Authorization header
- Expiración automática

### HTTP Security
- HTTPS en producción (configurar en servidor)
- CORS configurado en backend

### XSS Protection
- Vue escapa contenido por defecto
- No uso de `v-html` con contenido no confiable

### Route Protection
- Navigation guards
- Verificación de token en cada ruta protegida

## Escalabilidad

### Modularidad
- Estructura por features
- Componentes pequeños y reutilizables
- Services independientes

### Extensibilidad
- Fácil agregar nuevas vistas
- Fácil agregar nuevos stores
- Fácil agregar nuevos services

### Mantenibilidad
- TypeScript para type safety
- Código documentado
- Estructura clara y consistente

## Testing (No implementado aún)

### Recomendaciones para el futuro:

**Unit Testing**:
- Vitest para unit tests
- Testing de composables
- Testing de stores

**Component Testing**:
- Vue Test Utils
- Testing de componentes en aislamiento

**E2E Testing**:
- Playwright o Cypress
- Testing de flujos completos

## Decisiones Arquitectónicas

### ¿Por qué Vue 3 Composition API?
- Mejor organización de lógica
- Mejor TypeScript support
- Reutilización de código con composables

### ¿Por qué Pinia en vez de Vuex?
- Oficial para Vue 3
- Sintaxis más simple
- Mejor TypeScript
- No requiere mutaciones

### ¿Por qué Axios en vez de Fetch?
- Interceptores built-in
- Manejo de errores más fácil
- Transformación automática de JSON

### ¿Por qué TypeScript?
- Type safety
- Mejor developer experience
- Detección temprana de errores
- Mejor refactoring

### ¿Por qué Vite en vez de Webpack?
- Desarrollo más rápido
- HMR instantáneo
- Configuración más simple
- Build optimizado

## Diagramas

### Diagrama de Componentes

```
App.vue
├── Navbar.vue (si showNavbar)
├── SessionModal.vue (si showWarningModal)
└── RouterView
    ├── LoginView.vue
    └── DashboardView.vue
        ├── FastActionDashboard.vue
        ├── Report.vue
        └── Graphics.vue
            └── BarGraph.vue (múltiples instancias)
```

### Diagrama de Dependencias

```
main.ts
├── App.vue
├── router (Vue Router)
└── pinia (Pinia)

router
├── guards
└── lazy loaded views

Services
├── use API Clients
└── return Promises

API Clients
├── Axios interceptors
└── environment config
```

## Mejoras Futuras

1. **Testing Suite**: Implementar unit, component y e2e tests
2. **Error Boundary**: Componente global para capturar errores
3. **Internacionalización**: Vue I18n para múltiples idiomas
4. **Optimistic Updates**: Actualizar UI antes de confirmar con backend
5. **Offline Support**: Service workers para funcionalidad offline
6. **Performance Monitoring**: Integrar analytics y performance tracking
7. **Accessibility**: Mejorar ARIA labels y navegación por teclado
8. **Dark Mode**: Theme switcher
9. **Progressive Web App**: Convertir en PWA

## Referencias

- [Vue 3 Documentation](https://vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [Axios Documentation](https://axios-http.com/)
- [Vite Documentation](https://vitejs.dev/)
