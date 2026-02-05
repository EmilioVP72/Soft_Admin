# Guía Rápida - APIs de Ventas por Departamento

## 📊 Vista General del Sistema

```
┌─────────────────────────────────────────────────────────┐
│                   SISTEMA DE VENTAS                      │
└─────────────────────────────────────────────────────────┘
                            │
                ┌───────────┴───────────┐
                │                       │
         ┌──────▼──────┐        ┌──────▼──────┐
         │  TIENDAS    │        │   USUARIOS  │
         │ (stores)    │        │   (users)   │
         └──────┬──────┘        └──────┬──────┘
                │                      │
                │      ┌───────────────┤
                │      │               │
         ┌──────▼──────▼────┐
         │  TRANSACCIONES   │
         │ (transactions)   │
         └────────┬─────────┘
                  │
                  │ (1-a-Muchos)
                  │
         ┌────────▼─────────────┐
         │ DETALLES TRANSACCIÓN │
         │(transaction_details) │
         └────────┬─────────────┘
                  │
                  │ Referencia
                  │
         ┌────────▼──────────┐
         │  DEPARTAMENTOS    │
         │ (departments)     │
         └───────┬───────────┘
                 │
                 │ Pertenece a
                 │
         ┌───────▼────────────────┐
         │ DEPTO. GENERAL (padre) │
         │ (general_deps)         │
         └───────┬────────────────┘
                 │
                 │ Pertenece a
                 │
         ┌───────▼──────┐
         │   TIENDAS    │
         │  (stores)    │
         └──────────────┘
```

---

## 🔌 Endpoints Rápidos

| Endpoint | Método | Propósito | Parámetros |
|----------|--------|-----------|-----------|
| `/api/sales/byDepartment` | GET | Ventas totales por depto (todas tiendas) | - |
| `/api/sales/byStore/{id}` | GET | Ventas por depto de tienda específica | `{storeId}` |
| `/api/sales/byGeneralDepartment` | GET | Ventas por depto general | - |
| `/api/sales/byStore/{id}/filtered` | GET | Ventas con filtro de fechas | `{storeId}`, `start_date`, `end_date` |
| `/api/sales/transactions/department/{id}` | GET | Historial de transacciones | `{departmentId}` |

---

## 💡 Casos de Uso Comunes

### Caso 1: Dashboard General
**"Quiero ver cuánto vendió cada departamento en total"**

```bash
GET /api/sales/byDepartment
```

Respuesta incluye: Total de ventas por departamento de TODAS las tiendas

---

### Caso 2: Desempeño de Tienda
**"¿Cuánto vendió cada departamento en la Tienda Centro?"**

```bash
GET /api/sales/byStore/5
```

Respuesta incluye: Detalles de ventas por departamento solo de esa tienda

---

### Caso 3: Reporte Mensual
**"¿Cuánto vendió la Tienda Centro en enero?"**

```bash
GET /api/sales/byStore/5/filtered?start_date=2026-01-01&end_date=2026-01-31
```

Respuesta incluye: Ventas por departamento en ese período

---

### Caso 4: Auditoría de Departamento
**"¿Todas las transacciones del departamento de Electrónica?"**

```bash
GET /api/sales/transactions/department/1
```

Respuesta incluye: Cada venta de ese departamento con todos sus detalles

---

## 📝 Ejemplos en curl

### Ejemplo 1: Ventas generales
```bash
curl -X GET "http://localhost:8000/api/sales/byDepartment" \
  -H "Content-Type: application/json"
```

### Ejemplo 2: Ventas de una tienda
```bash
curl -X GET "http://localhost:8000/api/sales/byStore/5" \
  -H "Content-Type: application/json"
```

### Ejemplo 3: Con filtro de fechas
```bash
curl -X GET "http://localhost:8000/api/sales/byStore/5/filtered?start_date=2026-01-01&end_date=2026-01-31" \
  -H "Content-Type: application/json"
```

### Ejemplo 4: Transacciones de departamento
```bash
curl -X GET "http://localhost:8000/api/sales/transactions/department/1" \
  -H "Content-Type: application/json"
```

---

## 🎯 Estructura de Respuesta Exitosa (Ejemplo Real)

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

---

## 🚀 Implementación en Vue 3

### Composable para obtener ventas

```typescript
// src/composables/useSales.ts
import { ref } from 'vue'

export function useSales() {
  const sales = ref([])
  const loading = ref(false)
  const error = ref(null)

  const getSalesByDepartment = async () => {
    try {
      loading.value = true
      const response = await fetch('http://localhost:8000/api/sales/byDepartment')
      const data = await response.json()
      
      if (data.success) {
        sales.value = data.data
      } else {
        error.value = data.message
      }
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  const getSalesByStore = async (storeId) => {
    try {
      loading.value = true
      const response = await fetch(`http://localhost:8000/api/sales/byStore/${storeId}`)
      const data = await response.json()
      
      if (data.success) {
        sales.value = data.data
      }
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  return {
    sales,
    loading,
    error,
    getSalesByDepartment,
    getSalesByStore
  }
}
```

### Componente Vue

```vue
<script setup>
import { onMounted } from 'vue'
import { useSales } from '@/composables/useSales'

const { sales, loading, getSalesByDepartment } = useSales()

onMounted(() => {
  getSalesByDepartment()
})
</script>

<template>
  <div class="sales-container">
    <h1>Ventas por Departamento</h1>
    
    <div v-if="loading" class="loading">Cargando...</div>
    
    <table v-else class="sales-table">
      <thead>
        <tr>
          <th>Departamento</th>
          <th>Ventas Totales</th>
          <th>Transacciones</th>
          <th>Cantidad</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="sale in sales" :key="sale.id_department">
          <td>{{ sale.department }}</td>
          <td>${{ sale.total_sales.toFixed(2) }}</td>
          <td>{{ sale.total_transactions }}</td>
          <td>{{ sale.total_quantity }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.sales-table {
  width: 100%;
  border-collapse: collapse;
}

.sales-table th,
.sales-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.sales-table th {
  background-color: #f5f5f5;
  font-weight: bold;
}

.sales-table tbody tr:hover {
  background-color: #f9f9f9;
}
</style>
```

---

## 🔐 Seguridad

Actualmente los endpoints NO requieren autenticación. Si necesitas protegerlos:

```php
// En routes/Sale/sales.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/byDepartment', [SalesController::class, 'getSalesByDepartmentGeneral']);
    // ... resto de rutas
});
```

---

## 🐛 Troubleshooting

| Problema | Solución |
|----------|----------|
| `404 No hay datos` | Asegúrate de que hay transacciones en la BD |
| `404 Tienda no existe` | Verifica que el `storeId` sea válido |
| `Error de validación de fechas` | Usa formato `YYYY-MM-DD` en query params |
| `Error 500 interno` | Revisa logs en `storage/logs/laravel.log` |

---

## 📚 Archivos de Referencia

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Sale/SalesController.php
│   │   ├── Repositories/Sale/SalesRepository.php
│   │   ├── Requests/Sale/SalesFilterRequest.php
│   │   └── Resources/Sale/
│   │       ├── DepartmentSalesResource.php
│   │       ├── GeneralDepartmentSalesResource.php
│   │       └── TransactionResource.php
│   └── Models/
│       ├── Transaction.php
│       └── TransactionDetail.php
├── database/
│   ├── migrations/
│   │   ├── 2026_01_25_create_transactions_table.php
│   │   └── 2026_01_25_create_transaction_details_table.php
│   └── seeders/
│       └── TransactionSeeder.php
├── routes/
│   ├── Sale/sales.php
│   └── api.php (actualizado)
└── documentation/
    └── SALES_API.md
```

---

## ✅ Checklist de Implementación

- ✅ Migraciones creadas
- ✅ Modelos Eloquent configurados
- ✅ Repositorio con lógica de negocio
- ✅ Controlador con 5 métodos principales
- ✅ Validación de requests
- ✅ Resources para formato de respuesta
- ✅ Rutas registradas en api.php
- ✅ Documentación completa
- ✅ Ejemplos de seeding
- ✅ Ejemplos de uso en Vue 3

---

## 📞 Próximos Pasos

1. Ejecutar migraciones: `php artisan migrate`
2. (Opcional) Ejecutar seeder: `php artisan db:seed --class=TransactionSeeder`
3. Verificar rutas: `php artisan route:list | grep sales`
4. Probar endpoints con Postman o curl
5. Integrar con frontend Vue 3

