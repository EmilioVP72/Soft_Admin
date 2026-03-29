# Documentación de APIs (Pagos y Departamentos)

Todas las rutas están protegidas con el middleware `auth:api`, por lo que se requiere un Bearer Token en los headers:
`Authorization: Bearer <tu_token>`

Todas las respuestas exitosas siguen la convención estructural de `UtilResponse`:
```json
{
    "success": true,
    "data": { ... },
    "message": "Mensaje de éxito",
    "status": 200
}
```

---

## 1. Métodos de Pago (Payments)

Endpoint Base: `/api/payments`

### Obtener todos los métodos de pago
- **Método:** `GET`
- **Ruta:** `/api/payments`
- **Descripción:** Obtiene una lista de todos los métodos de pago.
- **Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id_payment": 1,
            "payment": "Efectivo",
            "description": "Pago en billetes y monedas",
            "created_at": "...",
            "updated_at": "..."
        }
    ],
    "message": "Métodos de pago obtenidos correctamente",
    "status": 200
}
```

### Obtener un método de pago por ID
- **Método:** `GET`
- **Ruta:** `/api/payments/{id}`

### Crear un método de pago
- **Método:** `POST`
- **Ruta:** `/api/payments`
- **Body Requerido:**
```json
{
    "payment": "Efectivo",
    "description": "Pago tradicional"
}
```

### Actualizar un método de pago
- **Método:** `PUT` o `PATCH`
- **Ruta:** `/api/payments/{id}`
- **Body (Campos opcionales):**
```json
{
    "payment": "Tarjeta de Crédito",
    "description": "Visa o Mastercard"
}
```

### Eliminar un método de pago
- **Método:** `DELETE`
- **Ruta:** `/api/payments/{id}`

---

## 2. Departamentos Generales (General Departments)

Endpoint Base: `/api/general-departments`

### Obtener todos
- **Método:** `GET`
- **Ruta:** `/api/general-departments`

### Obtener por ID
- **Método:** `GET`
- **Ruta:** `/api/general-departments/{id}`

### Crear
- **Método:** `POST`
- **Ruta:** `/api/general-departments`
- **Body Requerido:**
```json
{
    "g_departament": "Carnes",
    "g_descripcion": "Área de cárnicos generales",
    "fkl_id_tienda": 1
}
```

### Actualizar
- **Método:** `PUT/PATCH`
- **Ruta:** `/api/general-departments/{id}`
- **Body:** Igual que en POST (opcionales).

### Eliminar
- **Método:** `DELETE`
- **Ruta:** `/api/general-departments/{id}`

---

## 3. Departamentos por Tienda (Departments)

Endpoint Base: `/api/departments`

### Obtener todos
- **Método:** `GET`
- **Ruta:** `/api/departments`

### Obtener por ID
- **Método:** `GET`
- **Ruta:** `/api/departments/{id}`

### Crear
- **Método:** `POST`
- **Ruta:** `/api/departments`
- **Body Requerido:**
```json
{
    "department": "Carnes Rojas",
    "description": "Venta de res y cerdo",
    "fk1_id_general_dep": 1
}
```

### Actualizar
- **Método:** `PUT/PATCH`
- **Ruta:** `/api/departments/{id}`
- **Body:** Igual que en POST (opcionales).

### Eliminar
- **Método:** `DELETE`
- **Ruta:** `/api/departments/{id}`
