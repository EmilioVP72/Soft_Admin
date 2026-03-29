# Documentación de APIs: Proveedores, Entradas, Salidas y Ventas

Este documento detalla la estructura, propósito, relaciones de base de datos y la forma de integrar desde el Frontend las APIs desarrolladas para la gestión completa del ciclo de inventario, ventas y proveedores.

---

## 🏗️ 1. API de Proveedores (Suppliers)

### Propósito y Motivo
El propósito de esta API es gestionar el catálogo de proveedores de la empresa y registrar los pagos realizados a dichos proveedores por concepto de productos (departamentos funcionales). El motivo principal es mantener un control financiero exacto de los egresos generados hacia proveedores externos y permitir la trazabilidad del dinero.

### Tablas Relacionadas
- `suppliers` (Catálogo general de proveedores).
- `supplier_payments` (Tabla pivote/transaccional **NUEVA** donde se registra el monto y la fecha que se le pagó a un proveedor específico por un "departamento").
- `departments` (Usada como el producto o concepto por el que se está pagando).

### Integración en el Frontend y Ejemplos de Prueba

**A) Crear un Proveedor:**
```javascript
// POST /api/suppliers
const response = await fetch('/api/suppliers', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer YOUR_TOKEN' },
    body: JSON.stringify({
        supplier: "Coca-Cola Femsa"
    })
});
```

**B) Registrar un Pago a un Proveedor (Endpoint Clave):**
```javascript
// POST /api/suppliers/{id_supplier}/payments
const response = await fetch('/api/suppliers/1/payments', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer YOUR_TOKEN' },
    body: JSON.stringify({
        fk2_id_department: 5, // ID del producto/departamento que surtió
        amount_paid: 15000.50, // Monto obligatorio
        payment_date: "2026-03-15T10:30:00" // Fecha en que se le pagó
    })
});
```

**C) Obtener Todos los Pagos de un Proveedor:**
```javascript
// GET /api/suppliers/{id_supplier}/payments
const response = await fetch('/api/suppliers/1/payments', {
    headers: { 'Authorization': 'Bearer YOUR_TOKEN' }
});
const data = await response.json();
console.log(data.data); // Array con el historial de pagos de este proveedor
```

---

## 📦 2. API de Entradas (Inputs) y Salidas (Outputs)

### Propósito y Motivo
Estas dos APIs existen para registrar los movimientos de mercancía dentro de la tienda que no necesariamente son una "Venta". 
- **Entradas:** Registro de cuando llega nueva mercancía al almacén.
- **Salidas:** Registro de mermas, devoluciones a proveedor, o consumo interno.

El motivo de abstraerlas es tener controladores independientes para organizar lógicamente el código, pero en la base de datos se comportan como `transactions` con un tipo específico (`transaction_type` = `input` o `output`), y por requerimiento, **todas deben estar ligadas a un método de pago** (`fk3_id_payment`).

### Tablas Relacionadas
- `transactions` (Tabla principal donde se asigna el tipo de transacción y el método de pago `fk3_id_payment`).
- `transaction_details` (Los registros línea por línea de la entrada/salida).
- `payments` (El método de pago utilizado).

### Integración en el Frontend y Ejemplos de Prueba

La estructura de JSON para **Entradas y Salidas es idéntica**, solo cambia la ruta (`/inputs` o `/outputs`).

**Ejemplo: Registrar una Entrada (Llegada de Mercancía)**
```javascript
// POST /api/inputs
const response = await fetch('/api/inputs', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer YOUR_TOKEN' },
    body: JSON.stringify({
        fk1_id_store: 1,           // Tienda donde ingresa
        fk2_id_user: 2,            // Usuario que registra
        fk3_id_payment: 3,         // Método de pago involucrado (Ej: Transferencia)
        total_amount: 5000.00,     // Valor total del ingreso
        notes: "Llegada de pedido semanal",
        details: [                 // Array obligatorio con el detalle
            {
                fk2_id_department: 10,  // Producto
                quantity: 100,
                unit_price: 50.00,
                subtotal: 5000.00
            }
        ]
    })
});
```

---

## 💰 3. API de Ventas (Sales)

### Propósito y Motivo
Esta API gestiona el CRUD completo de las transacciones hacia los clientes finales. Anteriormente solo se utilizaba para reportes (Gets), pero el motivo de la actualización es permitir que el propio sistema pueda generar, modificar y eliminar notas de venta directamente desde la aplicación.

### Tablas Relacionadas
- `transactions` (Registros con `transaction_type` predeterminado a `'sale'`).
- `transaction_details` (Detalle de los productos vendidos).
- `payments` (Método de cobro).

### Integración en el Frontend y Ejemplos de Prueba

La creación de una Venta (`/api/sales`) utiliza la misma estructura anidada. El Backend automáticamente procesa el detalle abriendo una "transacción de SQL" (para asegurar que si falla la cabecera, no se guarde el detalle).

**Ejemplo: Crear una Nota de Venta**
```javascript
// POST /api/sales
const saleData = {
    fk1_id_store: 1,           // Tienda
    fk2_id_user: 5,            // Cajero
    fk3_id_payment: 1,         // ID Método de Pago (Ej: Efectivo)
    total_amount: 350.50,
    notes: "Venta de mostrador",
    details: [
        {
            fk2_id_department: 4,  // Refresco
            quantity: 2,
            unit_price: 25.00,
            subtotal: 50.00
        },
        {
            fk2_id_department: 8,  // Galletas
            quantity: 5,
            unit_price: 60.10,
            subtotal: 300.50
        }
    ]
};

const response = await fetch('/api/sales', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer YOUR_TOKEN' },
    body: JSON.stringify(saleData)
});

const result = await response.json();
console.log("Venta Registrada Exitosamente:", result.data);
```

### Visualización y Listados Generales
Para visualizar datos, todas soportan el método `GET`.
Ejemplo para ver el historial general de Ventas, con información del Usuario, Tienda y Método de pago ya anidados:

```javascript
// GET /api/sales
const response = await fetch('/api/sales', {
    headers: { 'Authorization': 'Bearer YOUR_TOKEN' }
});
const sales = await response.json();

sales.data.forEach(sale => {
    console.log(`Venta # ${sale.id_transaction} - Total: $${sale.total_amount}`);
    console.log(`Método de Pago Usado: ${sale.payment?.payment}`); // <- El método de pago ligado
});
```
