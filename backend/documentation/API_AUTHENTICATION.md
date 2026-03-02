# API de Autenticación - Documentación

## Descripción
Esta API proporciona endpoints para autenticación de usuarios usando JWT (JSON Web Tokens). El sistema utiliza `tymon/jwt-auth` para la generación y validación de tokens.

## Configuración Base
- **Base URL**: `http://localhost:8000/api`
- **Autenticación**: JWT Bearer Token
- **Formato de respuesta**: JSON

## Estructura de Respuesta

### Respuesta Exitosa (200, 201)
```json
{
    "flag": true,
    "code": 200,
    "message": "Mensaje de éxito",
    "data": {}
}
```

### Respuesta de Error (4xx, 5xx)
```json
{
    "flag": false,
    "code": 400,
    "message": "Mensaje de error",
    "data": {}
}
```

---

## Endpoints

### 1. Login (Autenticación)
Autentica un usuario con email y contraseña.

**Endpoint**: `POST /api/auth/login`

**Headers**:
```
Content-Type: application/json
```

**Body**:
```json
{
    "email": "usuario@example.com",
    "password": "password123"
}
```

**Respuesta Exitosa (200)**:
```json
{
    "flag": true,
    "code": 200,
    "message": "Autenticación exitosa",
    "data": {
        "user": {
            "id_user": 1,
            "user": "nombre_usuario",
            "email": "usuario@example.com",
            "phone": "1234567890",
            "email_verified_at": null,
            "created_at": "2026-01-28T10:30:00.000000Z",
            "updated_at": "2026-01-28T10:30:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

**Errores Comunes**:
- `401`: Credenciales incorrectas
- `422`: Email no existe

---

### 2. Registro (Crear Usuario)
Registra un nuevo usuario en el sistema.

**Endpoint**: `POST /api/auth/register`

**Headers**:
```
Content-Type: application/json
```

**Body**:
```json
{
    "user": "nombre_usuario",
    "email": "usuario@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "1234567890"
}
```

**Respuesta Exitosa (201)**:
```json
{
    "flag": true,
    "code": 201,
    "message": "Usuario registrado exitosamente",
    "data": {
        "user": {
            "id_user": 2,
            "user": "nombre_usuario",
            "email": "usuario@example.com",
            "phone": "1234567890",
            "email_verified_at": null,
            "created_at": "2026-01-28T10:35:00.000000Z",
            "updated_at": "2026-01-28T10:35:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

**Errores Comunes**:
- `422`: Email o usuario ya existe
- `422`: Las contraseñas no coinciden

---

### 3. Obtener Usuario Actual
Obtiene la información del usuario autenticado.

**Endpoint**: `GET /api/auth/me`

**Headers**:
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Respuesta Exitosa (200)**:
```json
{
    "flag": true,
    "code": 200,
    "message": "Usuario obtenido exitosamente",
    "data": {
        "id_user": 1,
        "user": "nombre_usuario",
        "email": "usuario@example.com",
        "phone": "1234567890",
        "email_verified_at": null,
        "created_at": "2026-01-28T10:30:00.000000Z",
        "updated_at": "2026-01-28T10:30:00.000000Z"
    }
}
```

**Errores Comunes**:
- `401`: Token inválido o expirado
- `401`: No autenticado

---

### 4. Cerrar Sesión (Logout)
Invalida el token JWT actual.

**Endpoint**: `POST /api/auth/logout`

**Headers**:
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Respuesta Exitosa (200)**:
```json
{
    "flag": true,
    "code": 200,
    "message": "Sesión cerrada exitosamente",
    "data": []
}
```

**Errores Comunes**:
- `401`: Token inválido o expirado

---

### 5. Renovar Token
Genera un nuevo token JWT sin cerrar la sesión.

**Endpoint**: `POST /api/auth/refresh`

**Headers**:
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Respuesta Exitosa (200)**:
```json
{
    "flag": true,
    "code": 200,
    "message": "Token renovado exitosamente",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

**Errores Comunes**:
- `401`: Token inválido o expirado

---

## Uso del Token

### Almacenamiento (Frontend)
El token debe almacenarse en `localStorage` o `sessionStorage`:

```javascript
// Almacenar token
localStorage.setItem('token', response.data.data.token);

// Obtener token
const token = localStorage.getItem('token');

// Eliminar token
localStorage.removeItem('token');
```

### Envío en Peticiones
Incluir el token en el header `Authorization`:

```javascript
// Ejemplo con Fetch
fetch('http://localhost:8000/api/auth/me', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})
```

```javascript
// Ejemplo con Axios
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// O en cada petición
axios.get('http://localhost:8000/api/auth/me', {
    headers: {
        'Authorization': `Bearer ${token}`
    }
})
```

---

## Tiempos de Expiración

El token JWT tiene un tiempo de expiración configurado. El valor `expires_in` en la respuesta indica los segundos de validez (por defecto: 3600 segundos = 1 hora).

### Renovación de Token
- Usar el endpoint `/api/auth/refresh` para obtener un nuevo token sin cerrar sesión
- Hacer esto antes de que expire el token actual

---

## Validación de Datos

### LoginRequest
- `email` (required, email, exists:users,email)
- `password` (required, string, min:6)

### RegisterRequest
- `user` (required, string, min:3, max:255, unique:users,user)
- `email` (required, email, max:255, unique:users,email)
- `password` (required, string, min:6, confirmed)
- `password_confirmation` (required)
- `phone` (nullable, string, max:20)

---

## Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | OK - Solicitud exitosa |
| 201 | Created - Recurso creado exitosamente |
| 400 | Bad Request - Solicitud inválida |
| 401 | Unauthorized - No autenticado o token inválido |
| 404 | Not Found - Recurso no encontrado |
| 422 | Unprocessable Entity - Error de validación |
| 500 | Internal Server Error - Error del servidor |

---

## Notas Importantes

1. **Seguridad**: El token JWT debe enviarse en HTTPS en producción
2. **CORS**: Asegúrate de configurar CORS correctamente en `config/cors.php`
3. **Contraseñas**: Siempre usar HTTPS para transmitir contraseñas
4. **Token Expiration**: Implementar lógica de renovación automática en el frontend
5. **Logout**: Eliminar el token del storage local al cerrar sesión
