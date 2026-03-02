# API de Empleados - Documentación Completa

## 📋 Índice
- [Descripción General](#descripción-general)
- [Estructura de la Tabla](#estructura-de-la-tabla)
- [Relaciones](#relaciones)
- [Endpoints Disponibles](#endpoints-disponibles)
- [Ejemplos de Uso](#ejemplos-de-uso)
- [Validaciones](#validaciones)
- [Códigos de Estado](#códigos-de-estado)

---

## 📝 Descripción General

El módulo de **Empleados** permite gestionar el personal de las tiendas. Cada empleado está vinculado a una tienda y opcionalmente a un usuario del sistema. El sistema incluye control completo de ciclo de vida de empleados con estado, fechas de contratación y eliminación suave.

**Características principales:**
- ✅ CRUD completo (GET, POST, PUT, DELETE)
- ✅ Soft delete (eliminación lógica)
- ✅ Relaciones con Users y Stores
- ✅ Búsqueda avanzada
- ✅ Filtros por estado y posición
- ✅ Gestión de múltiples documentos

---

## 🏗️ Estructura de la Tabla

```sql
CREATE TABLE employees (
  id_employee        INT PRIMARY KEY AUTO_INCREMENT,
  full_name          VARCHAR(255) NOT NULL,
  email              VARCHAR(255) UNIQUE NOT NULL,
  phone              VARCHAR(20),
  document_type      VARCHAR(20) DEFAULT 'DNI',
  document_number    VARCHAR(50) UNIQUE NOT NULL,
  position           ENUM('Manager', 'Supervisor', 'Cashier', 'Stock', 'Sales', 'Other') DEFAULT 'Other',
  salary             DECIMAL(10,2) DEFAULT 0,
  status             ENUM('Active', 'Inactive', 'On Leave') DEFAULT 'Active',
  hire_date          DATE NOT NULL,
  end_date           DATE,
  fk_id_user         INT FOREIGN KEY (users.id_user) ON DELETE SET NULL,
  fk_id_store        INT FOREIGN KEY (stores.id_store) ON DELETE CASCADE,
  notes              TEXT,
  created_at         TIMESTAMP,
  updated_at         TIMESTAMP,
  deleted_at         TIMESTAMP (soft delete)
);
```

### Campos Principales:
- **id_employee**: Identificador único
- **full_name**: Nombre completo (requerido)
- **email**: Correo único (requerido)
- **phone**: Teléfono opcional
- **document_type**: DNI, RUC, Pasaporte, Otro
- **document_number**: Número de documento único
- **position**: Puesto en la tienda
- **salary**: Salario mensual
- **status**: Active, Inactive, On Leave
- **hire_date**: Fecha de contratación
- **end_date**: Fecha de cese (opcional)
- **fk_id_user**: Usuario del sistema (opcional)
- **fk_id_store**: Tienda del empleado (requerido)

---

## 🔗 Relaciones

### Relación 1: Employee → User (Opcional)
Un empleado puede tener un usuario del sistema asignado (para login):
```php
$employee->user(); // belongsTo
$user->employees(); // hasMany
```

### Relación 2: Employee → Store (Requerida)
Todo empleado pertenece a una tienda:
```php
$employee->store(); // belongsTo
$store->employees(); // hasMany
```

---

## 🔌 Endpoints Disponibles

### 1. **GET /api/employees/all**
Obtener todos los empleados con sus relaciones

**Headers Requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleados obtenidos correctamente",
  "data": [
    {
      "id_employee": 1,
      "full_name": "Juan García",
      "email": "juan@example.com",
      "phone": "555-1234",
      "document_type": "DNI",
      "document_number": "12345678",
      "position": "Manager",
      "salary": 3000.00,
      "status": "Active",
      "status_label": "Activo",
      "hire_date": "2024-01-15",
      "end_date": null,
      "notes": null,
      "created_at": "2024-01-15T10:00:00Z",
      "updated_at": "2024-01-15T10:00:00Z",
      "user": {
        "id_user": 5,
        "name": "Juan García",
        "email": "juan@example.com"
      },
      "store": {
        "id_store": 1,
        "store": "Tienda Centro",
        "colony": "Centro",
        "street": "Calle Principal"
      }
    }
  ]
}
```

---

### 2. **GET /api/employees/{id}**
Obtener un empleado específico

**Parámetros:**
- `id` (requerido): ID del empleado

**URL Ejemplo:**
```
GET /api/employees/1
```

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleado encontrado correctamente",
  "data": {
    "id_employee": 1,
    "full_name": "Juan García",
    "email": "juan@example.com",
    ...
  }
}
```

**Response (404):**
```json
{
  "flag": false,
  "code": 404,
  "message": "El empleado no existe",
  "data": []
}
```

---

### 3. **POST /api/employees/create**
Crear un nuevo empleado

**Request Body:**
```json
{
  "full_name": "María López",
  "email": "maria@example.com",
  "phone": "555-5678",
  "document_type": "DNI",
  "document_number": "87654321",
  "position": "Supervisor",
  "salary": 2500.00,
  "status": "Active",
  "hire_date": "2024-02-01",
  "end_date": null,
  "fk_id_user": null,
  "fk_id_store": 1,
  "notes": "Asignado a tienda centro"
}
```

**Response (201):**
```json
{
  "flag": true,
  "code": 201,
  "message": "Empleado creado correctamente",
  "data": {
    "id_employee": 2,
    "full_name": "María López",
    ...
  }
}
```

---

### 4. **PUT /api/employees/update/{id}**
Actualizar un empleado existente

**Parámetros:**
- `id` (requerido): ID del empleado

**Request Body (todos los campos son opcionales):**
```json
{
  "salary": 2700.00,
  "status": "On Leave",
  "notes": "En licencia hasta fin de mes"
}
```

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleado actualizado correctamente",
  "data": {
    "id_employee": 1,
    "salary": 2700.00,
    "status": "On Leave",
    ...
  }
}
```

---

### 5. **DELETE /api/employees/delete/{id}**
Eliminar un empleado (soft delete)

**Parámetros:**
- `id` (requerido): ID del empleado

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleado eliminado correctamente",
  "data": null
}
```

---

### 6. **GET /api/employees/active**
Obtener solo empleados con estado "Active"

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleados activos obtenidos correctamente",
  "data": [...]
}
```

---

### 7. **GET /api/employees/store/{storeId}**
Obtener empleados de una tienda específica

**Parámetros:**
- `storeId` (requerido): ID de la tienda

**URL Ejemplo:**
```
GET /api/employees/store/1
```

---

### 8. **GET /api/employees/position/{position}**
Obtener empleados por posición

**Parámetros:**
- `position` (requerido): Manager, Supervisor, Cashier, Stock, Sales, Other

**URL Ejemplo:**
```
GET /api/employees/position/Manager
```

---

### 9. **GET /api/employees/search?q={query}**
Buscar empleados por nombre, email o número de documento

**Parámetros Query:**
- `q` (requerido, min 3 caracteres): Término de búsqueda

**URL Ejemplo:**
```
GET /api/employees/search?q=juan
```

**Response:**
```json
{
  "flag": true,
  "code": 200,
  "message": "Búsqueda completada correctamente",
  "data": [...]
}
```

---

### 10. **GET /api/employees/trashed**
Obtener empleados eliminados (soft deleted)

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleados eliminados obtenidos correctamente",
  "data": [...]
}
```

---

### 11. **PUT /api/employees/restore/{id}**
Restaurar un empleado que fue eliminado

**Parámetros:**
- `id` (requerido): ID del empleado eliminado

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleado restaurado correctamente",
  "data": {...}
}
```

---

### 12. **DELETE /api/employees/force-delete/{id}**
Eliminar permanentemente un empleado de la base de datos

**⚠️ ADVERTENCIA:** Esta acción es irreversible

**Response (200):**
```json
{
  "flag": true,
  "code": 200,
  "message": "Empleado eliminado permanentemente",
  "data": null
}
```

---

## 📚 Ejemplos de Uso

### Ejemplo 1: Crear un empleado con cURL

```bash
curl -X POST http://localhost/api/employees/create \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Carlos Mendez",
    "email": "carlos@example.com",
    "phone": "555-9012",
    "document_type": "DNI",
    "document_number": "11223344",
    "position": "Cashier",
    "salary": 1800.00,
    "status": "Active",
    "hire_date": "2024-02-20",
    "fk_id_store": 1,
    "notes": "Cajero principal"
  }'
```

### Ejemplo 2: Actualizar salario de un empleado

```bash
curl -X PUT http://localhost/api/employees/update/1 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "salary": 3500.00
  }'
```

### Ejemplo 3: Buscar empleados

```bash
curl -X GET "http://localhost/api/employees/search?q=juan" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### Ejemplo 4: Obtener empleados de una tienda

```bash
curl -X GET http://localhost/api/employees/store/1 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## ✅ Validaciones

### Validaciones en POST (Crear):

| Campo | Validación | Mensaje de Error |
|-------|-----------|------------------|
| `full_name` | requerido, string, max 255 | El nombre completo es obligatorio |
| `email` | requerido, email, único | El email debe ser válido y único |
| `phone` | opcional, string, max 20 | El teléfono debe ser válido |
| `document_type` | requerido, in: DNI,RUC,Pasaporte,Otro | Tipo de documento inválido |
| `document_number` | requerido, único, max 50 | El documento debe ser único |
| `position` | requerido, in: Manager, Supervisor... | Posición inválida |
| `salary` | requerido, numeric, min 0, max 999999.99 | Salario inválido |
| `status` | requerido, in: Active, Inactive, On Leave | Estado inválido |
| `hire_date` | requerido, date YYYY-MM-DD | Fecha de contratación inválida |
| `end_date` | opcional, date, >= hire_date | Fecha de fin debe ser posterior a inicio |
| `fk_id_user` | opcional, exists en users | Usuario no existe |
| `fk_id_store` | requerido, exists en stores | Tienda no existe |
| `notes` | opcional, string, max 1000 | Las notas son demasiado largas |

### Validaciones en PUT (Actualizar):
- Todos los campos son opcionales (PATCH/PUT parcial)
- El email y document_number ignoran el registro actual para validación unique

---

## 📊 Códigos de Estado HTTP

| Código | Significado | Caso de Uso |
|--------|-----------|-----------|
| **200** | OK | Operación exitosa (GET, PUT, DELETE) |
| **201** | Created | Empleado creado exitosamente |
| **400** | Bad Request | Búsqueda con menos de 3 caracteres |
| **404** | Not Found | Empleado o recurso no existe |
| **422** | Unprocessable Entity | Errores de validación |
| **500** | Internal Server Error | Error del servidor |
| **401** | Unauthorized | Falta token JWT |

---

## 🔐 Autenticación

Todos los endpoints requieren autenticación JWT. Incluya el token en el header:

```
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

---

## 🚀 Uso en Frontend (JavaScript/TypeScript)

```typescript
// Obtener todos los empleados
async function getEmployees() {
  const response = await fetch('/api/employees/all', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return response.json();
}

// Crear empleado
async function createEmployee(data) {
  const response = await fetch('/api/employees/create', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  });
  return response.json();
}

// Actualizar empleado
async function updateEmployee(id, data) {
  const response = await fetch(`/api/employees/update/${id}`, {
    method: 'PUT',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(data)
  });
  return response.json();
}

// Eliminar empleado
async function deleteEmployee(id) {
  const response = await fetch(`/api/employees/delete/${id}`, {
    method: 'DELETE',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    }
  });
  return response.json();
}
```

---

## 📌 Notas Importantes

1. **Soft Delete**: Los empleados eliminados no se eliminan realmente, solo se marcan como eliminados (deleted_at)
2. **Relación con Tienda**: La eliminación de una tienda cascada a eliminar sus empleados
3. **Relación con Usuario**: Si se desvincula un usuario, los empleados no se eliminan
4. **Status**: Es diferente a delete. Un empleado puede estar "Inactive" pero seguir en el sistema

---

## 🛠️ Mantenimiento

### Ejecutar Migraciones:
```bash
php artisan migrate
```

### Revertir Migraciones:
```bash
php artisan migrate:rollback
```

### Seeders (si se crean):
```bash
php artisan db:seed
```

---

**Versión:** 1.0  
**Última actualización:** 2026-01-25  
**Autor:** Senior Backend Engineer
