# Integración con la API

Configuración de clientes HTTP y servicios para comunicación con el backend.

## Clientes Axios

### Cliente Principal (con Auth)

**Ubicación**: `/src/api/axios.ts`

```typescript
import axios from 'axios'
import { useAuthStore } from '../stores/auth'

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    }
})

// Request Interceptor
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('token')
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`
    }
    return config
})

// Response Interceptor
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            const authStore = useAuthStore()
            authStore.logout()
            router.replace({ name: 'login' })
        }
        return Promise.reject(error)
    }
)

export default apiClient
```

### Cliente Stores (sin Auth)

**Ubicación**: `/src/api/stores.ts`

```typescript
import axios from 'axios'

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
    }
})

export default apiClient
```

## Servicios

### LoginServices

**Ubicación**: `/src/services/LoginServices.ts`

```typescript
import apiClient from '@/api/axios'

export default {
    loginUser(credentials: { email: string; password: string }) {
        return apiClient.post('/auth/login', credentials)
    },

    meUser(){
        return apiClient.get('/auth/me')
    },

    logoutUser() {
        return apiClient.post('/auth/logout')
    },

    refreshToken(){
        return apiClient.post('/auth/refresh')
    }
}
```

### StoresServices

**Ubicación**: `/src/services/StoresServices.ts`

```typescript
import apiClient from "../api/stores"
import type { Store } from "../interfaces/StoresInterfaces"

export default{
    getSalesByDepartment(){
        return apiClient.get<Store>('/sales/byDepartment')
    },

    getSalesByDepartmentByStore(storeId: number){
        return apiClient.get<Store>(`/sales/byStore/${storeId}`)
    },

    getStores(){
        return apiClient.get<Store[]>('/stores/all')
    }
}
```

## Variables de Entorno

**Archivo**: `.env`

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

**Uso**:
```typescript
import.meta.env.VITE_API_BASE_URL
```

**Importante**: Variables deben comenzar con `VITE_`

## Endpoints Disponibles

### Autenticación

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | /auth/login | Login de usuario | No |
| GET | /auth/me | Info del usuario actual | Sí |
| POST | /auth/logout | Cerrar sesión | Sí |
| POST | /auth/refresh | Renovar token | Sí |

### Stores/Ventas

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| GET | /sales/byDepartment | Ventas por departamento (todas las tiendas) | No |
| GET | /sales/byStore/:id | Ventas por departamento de una tienda | No |
| GET | /stores/all | Listar todas las tiendas | No |

## Manejo de Errores

```typescript
try {
  const response = await LoginServices.loginUser(credentials)
  // Éxito
} catch (error) {
  if(axios.isAxiosError(error)){
    const status = error.response?.status
    
    if (status === 401) {
      // No autorizado
    } else if(status === 422){
      // Errores de validación
    } else if(status === 404){
      // No encontrado
    } else if(status === 500){
      // Error del servidor
    }
  }
}
```

## Interceptores

### Request Interceptor
- Agrega token JWT automáticamente
- Se ejecuta antes de cada petición

### Response Interceptor
- Detecta 401 (no autorizado)
- Ejecuta logout automático
- Redirige a login

## Tipos TypeScript

**Interfaces**: `/src/interfaces/`

```typescript
// AuthInterfaces.ts
export interface LoginResponse {
    flag: boolean
    code: number
    message: string
    data: {
        user: {...}
        token: string
        token_type: "Bearer"
        expires_in: number
    }
}

// StoresInterfaces.ts
export interface Store {
    id: number
    name: string
    // ...
}
```

## Best Practices

✅ Usar servicios en vez de llamadas directas
✅ TypeScript types para responses
✅ Manejo de errores con try/catch
✅ Interceptores para cross-cutting concerns
✅ Variables de entorno para URLs

## Referencias

- [Axios Documentation](https://axios-http.com/)
