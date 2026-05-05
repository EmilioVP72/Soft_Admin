# Documentación: Sistema de Productos e Inventario

Este documento detalla los cambios realizados en el sistema, las nuevas funcionalidades implementadas, ejemplos de uso de la API y una guía para integrarlo en el frontend.

---

## 1. Cambios en la Base de Datos

Se agregaron dos nuevas tablas para gestionar los productos y sus respectivos inventarios (entradas y conciliaciones).

### Tabla: `products`
Gestiona el catálogo base de los productos y se vincula con los proveedores (`suppliers`).
- **id_product**: ID primario.
- **product**: Nombre del producto.
- **barcode**: Código de barras (único).
- **description**: Descripción del producto.
- **purchase_price**: Precio de compra.
- **sale_price**: Precio de venta.
- **unit**: Unidad de medida (pieza, kg, litro, etc.).
- **is_active**: Estado activo/inactivo (SoftDeletes).
- **fk1_id_supplier**: Llave foránea que relaciona el producto con un proveedor existente.

### Tabla: `product_inventories`
Lleva el registro de las entradas esperadas (por ticket de proveedor) contra las reales contadas en tienda.
- **id_product_inventory**: ID primario.
- **fk1_id_product**: Llave foránea hacia la tabla `products`.
- **ticket_quantity**: Cantidad esperada según el recibo/ticket.
- **physical_quantity**: Cantidad física real contada en el inventario.
- **difference**: Diferencia calculada automáticamente (`physical_quantity` - `ticket_quantity`).
- **status**: Estado actual (`pending` [pendiente de revisar], `verified` [exacto], `discrepancy` [sobrante/faltante]).
- **notes**: Notas u observaciones adicionales.
- **ticket_date**: Fecha original de la entrada/ticket.
- **verified_at**: Timestamp de cuándo se verificó físicamente.

---

## 2. Arquitectura Backend Implementada

Se implementó el flujo completo bajo el patrón existente del proyecto (`Route -> Controller -> Repository -> Request -> Resource`):

- **Modelos**: `App\Models\Product`, `App\Models\ProductInventory`
- **Repositorios**: `ProductRepository`, `ProductInventoryRepository`
- **Validaciones (Requests)**: 
  - `StoreProductRequest`, `UpdateProductRequest`
  - `StoreProductInventoryRequest`, `VerifyProductInventoryRequest`
- **Formateo JSON (Resources)**: `ProductResource`, `ProductInventoryResource`
- **Controladores**: `ProductController`, `ProductInventoryController`
- **Rutas**: Todas agrupadas bajo el prefijo correspondiente e inyectadas en `routes/api.php` bajo el middleware `auth:api`.

---

## 3. Endpoints y Ejemplos de Uso (APIs)

> **Nota:** Todos los endpoints requieren que se envíe el token de autenticación en los Headers:
> `Authorization: Bearer <tu_token_aqui>`

### Productos (`/api/products`)

**1. Obtener todos los productos**
- **GET** `/api/products`

**2. Crear un nuevo producto**
- **POST** `/api/products`
- **Body (JSON)**:
```json
{
  "product": "Coca Cola 600ml",
  "barcode": "7501055300075",
  "purchase_price": 12.50,
  "sale_price": 18.00,
  "unit": "pieza",
  "fk1_id_supplier": 1
}
```

**3. Actualizar un producto existente**
- **PUT** `/api/products/{id}`

**4. Obtener todos los registros de inventario de un producto**
- **GET** `/api/products/{id}/inventories`

---

### Inventarios (`/api/product-inventories`)

**1. Registrar una nueva entrada (Ticket)**
- **POST** `/api/product-inventories`
- **Body (JSON)**:
```json
{
  "fk1_id_product": 1,
  "ticket_quantity": 50,
  "status": "pending",
  "ticket_date": "2026-05-05 10:00:00"
}
```

**2. Conciliar Inventario (Verificación Física)**
Endpoint especial para ejecutar la lógica de negocio que compara la cantidad del ticket vs lo real en tienda.
- **POST** `/api/product-inventories/{id}/verify`
- **Body (JSON)**:
```json
{
  "physical_quantity": 48,
  "notes": "Llegaron 2 piezas rotas en la caja."
}
```
*El backend automáticamente calculará la diferencia (en este caso `-2`), establecerá el status como `discrepancy` y marcará la fecha `verified_at` con la fecha actual.*

---

## 4. Guía de Implementación en Frontend

A continuación se muestra un ejemplo genérico de cómo se deben consumir y manejar estas APIs en tu frontend (ejemplo asumiendo una instancia de `axios` o `fetch` global configurada con el token de sesión).

### 4.1. Servicio de Llamadas a la API (`services/inventory.js`)

```javascript
import api from './api'; // Tu instancia preconfigurada de Axios

// Productos
export const getProducts = () => api.get('/products');
export const createProduct = (data) => api.post('/products', data);

// Inventarios y Conciliación
export const registerTicket = (data) => api.post('/product-inventories', data);

// Enviar la cantidad física contada
export const verifyStock = (inventoryId, physicalQuantity, notes = '') => {
    return api.post(`/product-inventories/${inventoryId}/verify`, {
        physical_quantity: physicalQuantity,
        notes: notes
    });
};
```

### 4.2. Ejemplo de Componente de UI para Conciliar (React/Vue/Angular)

Lógica para la acción del botón "Verificar Stock Físico":

```javascript
// Suponiendo que el usuario contó las piezas físicamente y dio click en "Guardar Conciliación"
const handleVerifyStock = async (inventoryId, countedQuantity, notesText) => {
    try {
        const response = await verifyStock(inventoryId, countedQuantity, notesText);
        const { data } = response.data; // Desestructuramos la respuesta exitosa

        if (data.status === 'discrepancy') {
            console.warn(`Alerta de Discrepancia: Faltan/Sobran ${data.difference} unidades.`);
            // Mostrar notificación visual de color rojo/naranja al usuario
        } else if (data.status === 'verified') {
            console.log('Stock cuadró exactamente con el ticket.');
            // Mostrar notificación de éxito verde
        }
        
        // Recargar la tabla/lista de inventario de la vista
        fetchInventories();

    } catch (error) {
        console.error("Error al verificar inventario:", error);
        // Mostrar mensaje de error general
    }
};
```

### 4.3. Flujo sugerido de UX en la Pantalla:
1. Una tabla que liste los tickets `pending` (Entradas pendientes de conciliar).
2. Un botón **"Verificar"** por cada fila.
3. Al dar clic, abrir un **Modal** que muestre:
   - Producto y Cantidad Esperada (Ej. *Se esperaban 50*).
   - Un `input` numérico para ingresar "Cantidad física".
   - Un `textarea` opcional para "Notas".
4. Al mandar el formulario, llamar a `verifyStock()`.
