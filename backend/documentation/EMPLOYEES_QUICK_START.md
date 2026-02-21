# 🚀 Guía Rápida de Instalación - Módulo de Empleados

## ⚡ Instalación en 5 Minutos

### Paso 1: Ejecutar la Migración
```bash
cd backend
php artisan migrate
```

Verás algo como:
```
Migrating: 2026_01_25_create_employees_table
Migrated:  2026_01_25_create_employees_table (155.43ms)
```

### Paso 2: (Opcional) Insertar Datos de Prueba
```bash
php artisan db:seed --class=EmployeeSeeder
```

Resultado esperado:
```
✅ 8 empleados creados exitosamente.
```

### Paso 3: Verificar la Instalación
```bash
php artisan tinker
```

Luego dentro de tinker:
```php
>>> App\Models\Employee::count()
=> 8

>>> App\Models\Employee::with(['user', 'store'])->first()
=> App\Models\Employee {#5
    id_employee: 1,
    full_name: "Juan Carlos García López",
    email: "juan.garcia@sofadmin.com",
    ...
    store: App\Models\Store {#7
      id_store: 1,
      store: "tienda ejemplo",
      ...
    }
  }

>>> exit
```

### Paso 4: Probar con Postman
1. Abrir Postman
2. Importar: `backend/documentation/EMPLOYEES_POSTMAN_COLLECTION.json`
3. Configurar variables:
   - `BASE_URL`: `http://localhost` (ajustar según tu entorno)
   - `TOKEN`: Tu token JWT válido
4. Ejecutar requests de ejemplo

---

## 🔐 Obtener Token JWT

### Opción A: Con Postman
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}
```

**Respuesta:**
```json
{
  "flag": true,
  "code": 200,
  "message": "Login successful",
  "data": {
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {...}
  }
}
```

### Opción B: Con cURL
```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

---

## 📚 Documentación Completa

### Archivos de Documentación Disponibles:
1. **EMPLOYEES_API.md** - Guía técnica completa de endpoints
2. **EMPLOYEES_IMPLEMENTATION_SUMMARY.md** - Resumen de implementación
3. **EMPLOYEES_POSTMAN_COLLECTION.json** - Requests listos para Postman
4. **EMPLOYEES_QUICK_START.md** - Este archivo (acceso rápido)

---

## 🧪 Pruebas Rápidas (Sin Postman)

### Test 1: Obtener todos los empleados
```bash
curl -X GET http://localhost/api/employees/all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test 2: Crear un empleado
```bash
curl -X POST http://localhost/api/employees/create \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "full_name": "Nuevo Empleado",
    "email": "nuevo@example.com",
    "phone": "555-9999",
    "document_type": "DNI",
    "document_number": "99999999Z",
    "position": "Sales",
    "salary": 2000.00,
    "status": "Active",
    "hire_date": "2024-02-21",
    "fk_id_store": 1
  }'
```

### Test 3: Actualizar salario
```bash
curl -X PUT http://localhost/api/employees/update/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "salary": 3600.00
  }'
```

### Test 4: Buscar empleados
```bash
curl -X GET "http://localhost/api/employees/search?q=juan" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Test 5: Filtra empleados activos
```bash
curl -X GET http://localhost/api/employees/active \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📁 Archivos Creados

```
✅ app/Models/Employee.php
✅ app/Http/Controllers/Employee/EmployeeController.php
✅ app/Http/Repositories/Employee/EmployeeRepository.php
✅ app/Http/Requests/Employee/EmployeeRequest.php
✅ app/Http/Resources/Employee/EmployeeResource.php
✅ database/migrations/2026_01_25_create_employees_table.php
✅ database/seeders/EmployeeSeeder.php
✅ routes/Employee/employees.php
✅ documentation/EMPLOYEES_API.md
✅ documentation/EMPLOYEES_IMPLEMENTATION_SUMMARY.md
✅ documentation/EMPLOYEES_POSTMAN_COLLECTION.json
✅ documentation/EMPLOYEES_QUICK_START.md
```

---

## 🛠️ Troubleshooting

### Error: "SQLSTATE[HY000]: General error"
**Solución:**
```bash
php artisan migrate:refresh --step
```

### Error: "Authorization Token not found"
**Solución:**
1. Verificar que incluyas el header: `Authorization: Bearer {token}`
2. Verificar que el token no esté expirado
3. Obtener un nuevo token con `/api/auth/login`

### Error: "Validation error"
**Solución:**
- Verificar que todos los campos obligatorios estén presentes
- Validar tipos de datos (salary debe ser numérico, dates en formato YYYY-MM-DD)
- Verificar mensajes de error en la respuesta

### Las relaciones no se cargan
**Solución:**
```php
// Tienes que usar with() para cargar relaciones
Employee::with(['user', 'store'])->first();
```

---

## 🎯 Checklist de Implementación

- [x] Migración creada y ejecutada
- [x] Modelo con relaciones
- [x] Repository con métodos CRUD
- [x] Controller con 12 endpoints
- [x] Form Request con validaciones
- [x] Resource para respuestas JSON
- [x] Rutas API protegidas
- [x] Documentación completa
- [x] Seeder de datos de prueba
- [x] Colección de Postman
- [x] Relaciones bidireccionales (User ↔ Employee, Store ↔ Employee)

---

## 📊 Estructura de Respuesta Estándar

Todas las respuestas siguen este formato:

### Éxito (status 200, 201):
```json
{
  "flag": true,
  "code": 200,
  "message": "Mensaje descriptivo",
  "data": {...} o [...]
}
```

### Error (status 400, 404, 422, 500):
```json
{
  "flag": false,
  "code": 400,
  "message": "Descripción del error",
  "data": []
}
```

---

## 🔄 Flujo de Datos

```
Cliente (Frontend)
    ↓
Controllers (EmployeeController)
    ↓
Repository (EmployeeRepository)
    ↓
Models (Employee)
    ↓
Database (employees table)
    ↓
Response via Resource (EmployeeResource)
    ↓
Cliente (JSON respuesta)
```

---

## 💡 Mejores Prácticas

### 1. Siempre Usar Relaciones
```php
// ❌ MALO - N+1 queries
$employees = Employee::all();
foreach ($employees as $emp) {
    echo $emp->store->store; // Query por cada empleado
}

// ✅ BUENO - 1 query + 1 query for relations
$employees = Employee::with('store')->get();
foreach ($employees as $emp) {
    echo $emp->store->store; // Sin queries adicionales
}
```

### 2. Usar Scopes para Filtros
```php
// ✅ BUENO - Legible y reutilizable
$active = Employee::active()->get();
$managers = Employee::byPosition('Manager')->get();
```

### 3. Validaciones Early
```php
// ✅ BUENO - La validación ocurre en el Request
// El Controller recibe datos garantizadamente válidos
public function store(EmployeeRequest $request) {
    $data = $request->validated(); // Ya validado
}
```

---

## 🚀 Próximos Pasos Opcionales

1. **Auditoría**: Agregar paquete `laravel-auditing`
2. **Notificaciones**: Enviar email cuando se crea un empleado
3. **Export**: Exportar empleados a Excel/PDF
4. **Filtros Avanzados**: Filtrar por rango de salario, fechas, etc.
5. **Permisos**: Restricción basada en roles (admin, manager, etc.)

---

## 📞 Soporte

- Revisar `EMPLOYEES_API.md` para documentación técnica completa
- Revisar `EMPLOYEES_IMPLEMENTATION_SUMMARY.md` para detalles arquitectónicos
- Ejecutar ejemplos de cURL proporcionados
- Importar Postman collection para pruebas interactivas

---

## ✨ Características Implementadas

### CRUD Completo
✅ CREATE - POST /api/employees/create  
✅ READ - GET /api/employees/all  
✅ READ ONE - GET /api/employees/{id}  
✅ UPDATE - PUT /api/employees/update/{id}  
✅ DELETE - DELETE /api/employees/delete/{id}  

### Funcionalidades Avanzadas
✅ Búsqueda por nombre, email, documento  
✅ Filtro por estado (Active, Inactive, On Leave)  
✅ Filtro por tienda  
✅ Filtro por posición  
✅ Soft delete con restauración  
✅ Relaciones bidireccionales  
✅ Validación completa  
✅ Autenticación JWT  

---

**¡Listo para usar!** 🎉

Para comenzar:
```bash
php artisan migrate
php artisan db:seed --class=EmployeeSeeder
```

Luego abre Postman e importa `EMPLOYEES_POSTMAN_COLLECTION.json`

---

*Implementado: 2026-01-25*  
*Versión: 1.0*  
*Laravel: 11.x*  
*PHP: 8.2+*
