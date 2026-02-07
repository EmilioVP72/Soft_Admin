# Documentación de APIs de Ventas

## Descripción General

Se han creado dos APIs principales para obtener datos de ventas:

1. **Ventas Generales por Departamento** - Obtiene el total de ventas de cada departamento en TODAS las tiendas
2. **Ventas por Departamento de una Tienda Específica** - Obtiene el total de ventas de cada departamento en una tienda individual

---

## Endpoints

### 1. Obtener Ventas por Departamento (General)

**Descripción:** Obtiene el total de ventas de cada departamento agregadas de todas las tiendas.

```
GET /api/sales/byDepartment
```

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Ventas por departamento obtenidas correctamente",
    "data": [
        {
            "id_department": 1,
            "department": "Electrónica",
            "general_department": "Tienda General",
            "total_sales": 15000.50,
            "total_transactions": 25,
            "total_quantity": 150.00
        },
        {
            "id_department": 2,
            "department": "Ropa",
            "general_department": "Tienda General",
            "total_sales": 8500.75,
            "total_transactions": 45,
            "total_quantity": 320.00
        }
    ]
}
```

**Parámetros:** Ninguno

**Códigos de Respuesta:**
- `200` - Éxito
- `404` - No hay datos disponibles
- `500` - Error interno del servidor

---

### 2. Obtener Ventas por Departamento de una Tienda Específica

**Descripción:** Obtiene el total de ventas de cada departamento para una tienda específica.

```
GET /api/sales/byStore/{storeId}
```

**Parámetros de Ruta:**
- `storeId` (requerido) - ID de la tienda

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Ventas por departamento de la tienda obtenidas correctamente",
    "data": [
        {
            "id_department": 1,
            "department": "Electrónica",
            "general_department": "Tienda General",
            "id_store": 5,
            "store": "Tienda Centro",
            "total_sales": 5000.25,
            "total_transactions": 10,
            "total_quantity": 50.00
        },
        {
            "id_department": 2,
            "department": "Ropa",
            "general_department": "Tienda General",
            "id_store": 5,
            "store": "Tienda Centro",
            "total_sales": 3200.50,
            "total_transactions": 20,
            "total_quantity": 120.00
        }
    ]
}
```

**Ejemplo de Uso:**
```
GET /api/sales/byStore/5
```

**Códigos de Respuesta:**
- `200` - Éxito
- `404` - Tienda no encontrada o sin datos
- `500` - Error interno del servidor

---

### 3. Obtener Ventas por Departamento General

**Descripción:** Obtiene el total de ventas agregadas por departamento general (nivel superior) en todas las tiendas.

```
GET /api/sales/byGeneralDepartment
```

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Ventas por departamento general obtenidas correctamente",
    "data": [
        {
            "id_general_dep": 1,
            "general_department": "Tienda General",
            "total_sales": 23500.25,
            "total_transactions": 70,
            "total_quantity": 470.00
        }
    ]
}
```

**Parámetros:** Ninguno

**Códigos de Respuesta:**
- `200` - Éxito
- `404` - No hay datos disponibles
- `500` - Error interno del servidor

---

### 4. Obtener Ventas de una Tienda con Filtro de Fechas

**Descripción:** Obtiene ventas por departamento de una tienda específica con filtro opcional de rango de fechas.

```
GET /api/sales/byStore/{storeId}/filtered
```

**Parámetros de Ruta:**
- `storeId` (requerido) - ID de la tienda

**Parámetros de Query (opcionales):**
- `start_date` - Fecha de inicio (formato: YYYY-MM-DD)
- `end_date` - Fecha final (formato: YYYY-MM-DD)

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Ventas por departamento obtenidas correctamente",
    "data": [
        {
            "id_department": 1,
            "department": "Electrónica",
            "general_department": "Tienda General",
            "total_sales": 2000.00,
            "total_transactions": 5,
            "total_quantity": 25.00
        }
    ]
}
```

**Ejemplo de Uso:**
```
GET /api/sales/byStore/5/filtered?start_date=2026-01-01&end_date=2026-01-31
```

**Códigos de Respuesta:**
- `200` - Éxito
- `404` - Tienda no encontrada
- `422` - Validación fallida (fechas inválidas)
- `500` - Error interno del servidor

---

### 5. Obtener Transacciones de un Departamento

**Descripción:** Obtiene el historial detallado de transacciones para un departamento específico.

```
GET /api/sales/transactions/department/{departmentId}
```

**Parámetros de Ruta:**
- `departmentId` (requerido) - ID del departamento

**Respuesta Exitosa (200):**
```json
{
    "success": true,
    "message": "Transacciones del departamento obtenidas correctamente",
    "data": [
        {
            "id_transaction": 1,
            "id_store": 5,
            "store_name": "Tienda Centro",
            "user_name": "Juan Pérez",
            "total_amount": 500.00,
            "transaction_type": "sale",
            "notes": "Venta regular",
            "transaction_date": "2026-01-25 10:30:45",
            "details": [
                {
                    "id_transaction_detail": 1,
                    "department": "Electrónica",
                    "quantity": 2.00,
                    "unit_price": 250.00,
                    "subtotal": 500.00
                }
            ],
            "created_at": "2026-01-25 10:30:45",
            "updated_at": "2026-01-25 10:30:45"
        }
    ]
}
```

**Ejemplo de Uso:**
```
GET /api/sales/transactions/department/1
```

**Códigos de Respuesta:**
- `200` - Éxito
- `404` - Departamento no encontrado o sin transacciones
- `500` - Error interno del servidor

---

## Estructura de Respuesta

### Respuesta Exitosa:
```json
{
    "success": true,
    "message": "Descripción del éxito",
    "data": {}
}
```

### Respuesta de Error:
```json
{
    "success": false,
    "message": "Descripción del error"
}
```

---

## Estructura de Base de Datos

### Tabla: transactions
```sql
CREATE TABLE transactions (
    id_transaction BIGSERIAL PRIMARY KEY,
    fk1_id_store BIGINT NOT NULL,
    fk2_id_user BIGINT NOT NULL,
    total_amount DECIMAL(12,2),
    transaction_type VARCHAR(255),
    notes TEXT,
    transaction_date TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabla: transaction_details
```sql
CREATE TABLE transaction_details (
    id_transaction_detail BIGSERIAL PRIMARY KEY,
    fk1_id_transaction BIGINT NOT NULL,
    fk2_id_department BIGINT NOT NULL,
    quantity DECIMAL(10,2),
    unit_price DECIMAL(12,2),
    subtotal DECIMAL(12,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Relaciones

```
Transaction (Ventas)
├── belongsTo: Store (Tienda)
├── belongsTo: User (Usuario)
└── hasMany: TransactionDetail (Detalles)
    └── belongsTo: Department (Departamento)
```

---

## Ejemplos de Uso en el Frontend (Vue/JavaScript)

### Obtener ventas generales por departamento:
```javascript
async function getSalesByDepartment() {
    try {
        const response = await fetch('http://localhost:8000/api/sales/byDepartment');
        const data = await response.json();
        
        if (data.success) {
            console.log('Ventas:', data.data);
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Error al obtener ventas:', error);
    }
}
```

### Obtener ventas de una tienda específica:
```javascript
async function getSalesForStore(storeId) {
    try {
        const response = await fetch(`http://localhost:8000/api/sales/byStore/${storeId}`);
        const data = await response.json();
        
        if (data.success) {
            console.log('Ventas de la tienda:', data.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

### Obtener ventas con filtro de fechas:
```javascript
async function getSalesWithDateRange(storeId, startDate, endDate) {
    try {
        const params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate
        });
        
        const response = await fetch(
            `http://localhost:8000/api/sales/byStore/${storeId}/filtered?${params}`
        );
        const data = await response.json();
        
        if (data.success) {
            console.log('Ventas filtradas:', data.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

---

## Archivos Creados

### Migraciones:
- `database/migrations/2026_01_25_create_transactions_table.php`
- `database/migrations/2026_01_25_create_transaction_details_table.php`

### Modelos:
- `app/Models/Transaction.php`
- `app/Models/TransactionDetail.php`

### Controladores:
- `app/Http/Controllers/Sale/SalesController.php`

### Repositorios:
- `app/Http/Repositories/Sale/SalesRepository.php`

### Requests:
- `app/Http/Requests/Sale/SalesFilterRequest.php`

### Resources:
- `app/Http/Resources/Sale/DepartmentSalesResource.php`
- `app/Http/Resources/Sale/GeneralDepartmentSalesResource.php`
- `app/Http/Resources/Sale/TransactionResource.php`

### Rutas:
- `routes/Sale/sales.php`
- Actualizado: `routes/api.php`

---

## Pasos para Ejecutar

1. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

2. **Poblar datos de prueba (opcional):**
   Crear seeders para insertar transacciones de prueba

3. **Verificar rutas:**
   ```bash
   php artisan route:list | grep sales
   ```

4. **Usar las APIs:**
   - Via Postman, curl, o desde el frontend
   - Todas las rutas están disponibles en `/api/sales`

---

## Notas Importantes

- ✅ Las APIs siguen el patrón de tu proyecto (Controller → Repository → Resource)
- ✅ Incluyen validación, manejo de errores y respuestas consistentes
- ✅ Usan el trait `UtilResponse` para respuestas estandarizadas
- ✅ Las migraciones crean relaciones con claves foráneas adecuadas
- ✅ Soportan filtrado por fechas en la API de tienda específica
- ✅ Los índices en las tablas optimizan las consultas de agregación

