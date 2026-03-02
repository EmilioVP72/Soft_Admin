# Sistema de Autenticación

Este documento describe el sistema de autenticación implementado en el frontend.

## Arquitectura de Autenticación

### Stack de Autenticación

- **Mecanismo**: JWT (JSON Web Tokens)
- **Almacenamiento**: LocalStorage
- **Gestión de Estado**: Pinia Store (auth.ts)
- **HTTP Client**: Axios con interceptores
- **Validación**: Zod para formularios

## Flujo de Autenticación

### 1. Login Flow

```
┌─────────────┐
│   Usuario   │
│ Ingresa     │
│ credenciales│
└──────┬──────┘
       │
       ▼
┌──────────────────────┐
│   LoginView.vue      │
│ - Validación Zod     │
│ - FormData reactive  │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ LoginServices.ts     │
│ loginUser()          │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│   Axios Client       │
│ POST /auth/login     │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│   Backend API        │
│ Valida credenciales  │
│ Genera JWT           │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Response:            │
│ {                    │
│   token: "...",      │
│   expires_in: 3600   │
│ }                    │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ authStore.ts         │
│ setAuthData()        │
│ - Guarda token       │
│ - Calcula expiración │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Vue Router           │
│ Redirige a /dashboard│
└──────────────────────┘
```

### 2. Authenticated Request Flow

```
┌──────────────────────┐
│   Componente Vue     │
│ Llama a Service      │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│   Service            │
│ apiClient.get()      │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────────────────┐
│   Axios Request Interceptor      │
│ - Lee token de localStorage      │
│ - Agrega Authorization header    │
│   "Bearer <token>"               │
└──────┬───────────────────────────┘
       │
       ▼
┌──────────────────────┐
│   Backend API        │
│ Valida JWT           │
│ Retorna datos        │
└──────┬───────────────┘
       │
       ▼ (Si 401)
┌──────────────────────────────────┐
│   Axios Response Interceptor     │
│ - Detecta 401                    │
│ - authStore.logout()             │
│ - router.replace('/login')       │
└──────────────────────────────────┘
```

### 3. Logout Flow

```
┌──────────────────────┐
│   Navbar.vue         │
│ Usuario click        │
│ "Cerrar Sesión"      │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ LoginServices.ts     │
│ logoutUser()         │
│ POST /auth/logout    │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ authStore.logout()   │
│ - token = null       │
│ - localStorage.clear()│
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Router push          │
│ → /login             │
└──────────────────────┘
```

### 4. Token Refresh Flow

```
┌──────────────────────────┐
│ useSessionWarning.ts     │
│ Timer detecta:           │
│ Faltan 5 min para expirar│
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────┐
│ SessionModal.vue     │
│ Muestra advertencia  │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Usuario confirma     │
│ "Mantener Sesión"    │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ LoginServices.ts     │
│ refreshToken()       │
│ POST /auth/refresh   │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ Backend retorna      │
│ nuevo token          │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ authStore.setAuthData│
│ - Actualiza token    │
│ - Nueva expiración   │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│ SessionModal cierra  │
│ Timer se reinicia    │
└──────────────────────┘
```

## Implementación Detallada

### Auth Store (Pinia)

**Ubicación**: `/src/stores/auth.ts`

```typescript
import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useAuthStore = defineStore('auth', () => {
    // State
    const token = ref<string | null>(localStorage.getItem('token'));

    // Actions
    function setAuthData(newToken: string, expiresIn: number) { 
        // Guardar token en estado
        token.value = newToken;
        localStorage.setItem('token', newToken);

        // Calcular y guardar tiempo de expiración
        const expirationTime = Date.now() + (expiresIn * 1000);
        localStorage.setItem('tokenExpiration', expirationTime.toString());
    }

    function logout() {
        // Limpiar estado
        token.value = null;
        // Limpiar localStorage
        localStorage.clear();
    }

    return { token, setAuthData, logout }
})
```

**Datos Almacenados en LocalStorage**:
- `token`: JWT string
- `tokenExpiration`: Timestamp en milisegundos

### Login Service

**Ubicación**: `/src/services/LoginServices.ts`

```typescript
import apiClient from '@/api/axios';

export default {
    // Login
    loginUser(credentials: { email: string; password: string }) {
        return apiClient.post('/auth/login', credentials);
    },

    // Obtener usuario actual
    meUser(){
        return apiClient.get('/auth/me');
    },

    // Logout
    logoutUser() {
        return apiClient.post('/auth/logout');
    },

    // Renovar token
    refreshToken(){
        return apiClient.post('/auth/refresh');
    }
}
```

### Axios Client con Interceptores

**Ubicación**: `/src/api/axios.ts`

```typescript
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    }
});

// Request Interceptor: Agregar Authorization header
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
});

// Response Interceptor: Manejar 401 Unauthorized
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

export default apiClient;
```

### Login Component

**Ubicación**: `/src/views/auth/LoginView.vue`

#### Validación con Zod

```typescript
import {z} from 'zod';

const loginSchema = z.object({
  email: z.string()
    .min(1, { message: "El correo electrónico es obligatorio." })
    .email({ message: "El correo electrónico no es válido." }),
  password: z.string()
    .min(6, { message: "La contraseña debe tener al menos 6 caracteres." })
    .min(1, { message: "La contraseña es obligatoria." })
})

type LoginData = z.infer<typeof loginSchema>;
```

#### Submit Handler

```typescript
const submitForm = async () => {
  isLoading.value = true;
  formErrors.value = {};
  
  // Validar
  if(!validate()){
    isLoading.value = false;
    return;
  }
  
  try {
    // Login
    const response = await LoginServices.loginUser({
      email: formData.email, 
      password: formData.password
    });
    
    // Guardar token
    authStore.setAuthData(
      response.data.data.token, 
      response.data.data.expires_in
    );
    
    // Redirigir
    router.push({ name: 'dashboard' });

  } catch (error) {
    // Manejo de errores
    if(axios.isAxiosError(error)){
      const status = error.response?.status;
      if (status === 401) {
        formErrors.value.password = "Credenciales inválidas.";
        formErrors.value.email = "Credenciales inválidas.";
      } else if(status === 422){
        formErrors.value = error.response?.data || {};
      }
    }
  } finally {
    isLoading.value = false;
  }
}
```

## Route Guards

**Ubicación**: `/src/router/index.ts`

### Meta Fields

```typescript
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
```

### Global Navigation Guard

```typescript
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  
  if(to.meta.requiresAuth) {
    // Ruta requiere autenticación
    if (authStore.token) {
      next(); // Token existe, permitir acceso
    } else {
      next({ name: 'login' }); // Sin token, redirigir a login
    }
  } else {
    // Ruta no requiere auth
    if(to.name === 'login' && authStore.token){
      // Ya autenticado, ir a dashboard
      next({ name: 'dashboard' });
    } else {
      next(); // Permitir acceso
    }
  }
});
```

## Sistema de Advertencia de Sesión

**Ubicación**: `/src/composables/useSessionWarning.ts`

### Funcionalidad

- Calcula tiempo hasta expiración del token
- Muestra modal 5 minutos antes de expirar
- Permite renovar token o cerrar sesión

### Implementación

```typescript
export function useSessionWarning() {
  const showWarningModal = ref(false)
  const isLoading = ref(false)
  let modalTimer: number | null = null

  // Calcular cuándo mostrar advertencia
  const calculateWarningTime = () => {
    const tokenExpiration = localStorage.getItem('tokenExpiration')
    if (!tokenExpiration) return null
    
    const expirationTime = parseInt(tokenExpiration)
    const currentTime = Date.now()
    const timeUntilExpiration = expirationTime - currentTime
    const fiveMinutesInMs = 5 * 60 * 1000
    
    return timeUntilExpiration - fiveMinutesInMs
  }

  // Iniciar timer
  const startWarningTimer = () => {
    if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }

    const timeUntilWarning = calculateWarningTime()
    if (timeUntilWarning === null) return
    
    if (timeUntilWarning > 0) {
      modalTimer = window.setTimeout(() => {
        showWarningModal.value = true
      }, timeUntilWarning)
    } else {
      showWarningModal.value = true
    }
  }

  // Renovar token
  const refreshToken = async () => {
    isLoading.value = true
    try {
      const response = await LoginServices.refreshToken()
      authStore.setAuthData(
        response.data.data.token, 
        response.data.data.expires_in
      )
      showWarningModal.value = false
      startWarningTimer() // Reiniciar timer
    } catch (error) {
      logout()
    } finally {
      isLoading.value = false
    }
  }

  // Logout
  const logout = () => {
    if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }
    showWarningModal.value = false
    authStore.logout()
    router.push({ name: 'login' })
  }

  // Watch route changes
  watch(() => route.name, (newRoute) => {
    if (newRoute === 'dashboard' && authStore.token) {
      startWarningTimer()
    } else if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }
  }, { immediate: true })

  return {
    showWarningModal,
    isLoading,
    refreshToken,
    logout
  }
}
```

## Interfaces TypeScript

**Ubicación**: `/src/interfaces/AuthInterfaces.ts`

```typescript
export interface LoginResponse {
    flag: boolean;
    code: number;
    message: string;
    data: {
        user: {
            id_user: number;
            email: string;
            phone: string;
            email_verified_at: string | null;
            created_at: string;
            updated_at: string;
        },
        token: string;
        token_type: "Bearer";
        expires_in: number;
    }
}
```

## Seguridad

### Buenas Prácticas Implementadas

#### ✅ 1. Token en Authorization Header
```typescript
config.headers['Authorization'] = `Bearer ${token}`;
```
- No se envía en query params (vulnerable a logs)
- No se envía en cookies (CSRF)

#### ✅ 2. HTTPS en Producción
- Los tokens deben transmitirse solo sobre HTTPS
- Configurar en servidor web (Nginx, Apache)

#### ✅ 3. Token Expiration
```typescript
const expirationTime = Date.now() + (expiresIn * 1000);
localStorage.setItem('tokenExpiration', expirationTime.toString());
```
- Tokens tienen tiempo de vida limitado
- Sistema de renovación antes de expirar

#### ✅ 4. Automatic Logout on 401
```typescript
if (error.response?.status === 401) {
    authStore.logout();
    router.replace({ name: 'login' });
}
```
- Logout automático en errores de autenticación
- Previene uso de tokens inválidos

#### ✅ 5. Validación Client-Side
```typescript
const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(6)
})
```
- Validación antes de enviar al servidor
- Reduce carga en backend
- Mejora UX con feedback inmediato

### Consideraciones de Seguridad

#### ⚠️ LocalStorage vs HttpOnly Cookies

**Actual**: LocalStorage
**Riesgo**: Vulnerable a XSS (Cross-Site Scripting)

**Mitigación**:
- Vue escapa contenido por defecto
- No usar `v-html` con contenido no confiable
- Sanitizar inputs del usuario

**Alternativa Futura**: HttpOnly Cookies
- Inmune a XSS
- Requiere cambios en backend (CSRF protection)

#### ⚠️ Token Storage

**Recomendación**: 
- Para aplicaciones de alto riesgo: HttpOnly Cookies + CSRF tokens
- Para aplicaciones normales: LocalStorage con buenas prácticas XSS

#### ✅ Password Best Practices

- Mínimo 6 caracteres (validación client-side)
- Backend debe validar complejidad
- Backend debe hashear (bcrypt, argon2)
- No se almacena en frontend

## Endpoints de Autenticación

### POST /auth/login
**Request**:
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response 200**:
```json
{
  "flag": true,
  "code": 200,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

**Response 401**:
```json
{
  "flag": false,
  "code": 401,
  "message": "Invalid credentials"
}
```

### POST /auth/logout
**Headers**:
```
Authorization: Bearer <token>
```

**Response 200**:
```json
{
  "flag": true,
  "code": 200,
  "message": "Logged out successfully"
}
```

### POST /auth/refresh
**Headers**:
```
Authorization: Bearer <token>
```

**Response 200**:
```json
{
  "flag": true,
  "code": 200,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

### GET /auth/me
**Headers**:
```
Authorization: Bearer <token>
```

**Response 200**:
```json
{
  "flag": true,
  "code": 200,
  "data": {
    "user": "John Doe"
  }
}
```

## Testing de Autenticación

### Casos de Prueba Recomendados

1. **Login exitoso**
   - Credenciales válidas → token guardado → redirección

2. **Login fallido**
   - Credenciales inválidas → mensaje de error → permanece en login

3. **Validación de formulario**
   - Email inválido → error mostrado
   - Password corto → error mostrado

4. **Acceso a rutas protegidas**
   - Sin token → redirige a login
   - Con token → acceso permitido

5. **Logout**
   - Click en logout → token eliminado → redirige a login

6. **Renovación de token**
   - Modal aparece 5 min antes
   - Confirmar → nuevo token → modal cierra
   - Cancelar → logout → redirige a login

7. **Token expirado**
   - Request con token expirado → 401 → logout automático

## Troubleshooting

### "Token not found" en requests

**Causa**: El token no está en localStorage

**Solución**:
```typescript
const token = localStorage.getItem('token');
console.log('Token:', token);
```

### Modal de sesión no aparece

**Causa**: `tokenExpiration` no guardado correctamente

**Solución**:
```typescript
const expiration = localStorage.getItem('tokenExpiration');
console.log('Expiration:', expiration);
console.log('Current time:', Date.now());
```

### Redirect loop entre login y dashboard

**Causa**: Guard lógica incorrecta

**Solución**: Verificar que el token esté guardado antes de redirigir

### 401 en todas las requests

**Causa**: Token inválido o expirado

**Solución**: Hacer logout manual y login de nuevo

## Mejoras Futuras

1. **Remember Me**: Opción para tokens de larga duración
2. **Refresh Token**: Separar access token y refresh token
3. **2FA**: Autenticación de dos factores
4. **Password Reset**: Flujo de recuperación de contraseña
5. **Session Management**: Ver sesiones activas
6. **Biometric Auth**: Soporte para huella/Face ID
7. **OAuth**: Login con Google, GitHub, etc.

## Referencias

- [JWT.io](https://jwt.io/)
- [OWASP Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html)
- [Vue Router Navigation Guards](https://router.vuejs.org/guide/advanced/navigation-guards.html)
- [Axios Interceptors](https://axios-http.com/docs/interceptors)
