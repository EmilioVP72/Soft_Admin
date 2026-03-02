# 🚀 Guía de Ejecución - APIs de Ventas

## Preparación Rápida (5 minutos)

### Paso 1: Ejecutar Migraciones
```bash
cd backend
php artisan migrate
```

**Qué sucede:**
- ✅ Se crean las tablas `transactions` y `transaction_details`
- ✅ Se configuran las relaciones (foreign keys)
- ✅ Se crean los índices para optimizar queries

**Esperado en terminal:**
```
Migrating: 2026_01_25_create_transactions_table.php
Migrated:  2026_01_25_create_transactions_table.php (0.50s)
Migrating: 2026_01_25_create_transaction_details_table.php
Migrated:  2026_01_25_create_transaction_details_table.php (0.45s)
```

---

### Paso 2: (Opcional) Cargar Datos de Prueba

```bash
php artisan db:seed --class=TransactionSeeder
```

**Qué sucede:**
- ✅ Se generan 50 transacciones aleatorias
- ✅ Se crean detalles para cada transacción
- ✅ Se asignan departamentos y tiendas aleatoriamente

**Esperado en terminal:**
```
Seeding: Database\Seeders\TransactionSeeder
Seeded:  Database\Seeders\TransactionSeeder (2.34s)
Se han creado 50 transacciones de prueba correctamente.
```

---

### Paso 3: Verificar Rutas Creadas

```bash
php artisan route:list | grep sales
```

**Esperado:**
```
GET       api/sales/byDepartment
GET       api/sales/byGeneralDepartment
GET       api/sales/byStore/{storeId}
GET       api/sales/byStore/{storeId}/filtered
GET       api/sales/transactions/department/{departmentId}
```

---

### Paso 4: Probar con curl

#### Test 1: Obtener ventas generales
```bash
curl -X GET "http://localhost:8000/api/sales/byDepartment" \
  -H "Content-Type: application/json"
```

**Respuesta esperada:**
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
    }
  ]
}
```

#### Test 2: Obtener ventas de una tienda (reemplaza 5 con un id válido)
```bash
curl -X GET "http://localhost:8000/api/sales/byStore/5" \
  -H "Content-Type: application/json"
```

#### Test 3: Con filtro de fechas
```bash
curl -X GET "http://localhost:8000/api/sales/byStore/5/filtered?start_date=2026-01-01&end_date=2026-01-31" \
  -H "Content-Type: application/json"
```

#### Test 4: Transacciones de un departamento
```bash
curl -X GET "http://localhost:8000/api/sales/transactions/department/1" \
  -H "Content-Type: application/json"
```

---

## Pruebas en Postman (Alternativa Gráfica)

### Crear Colección en Postman:

**1. Nuevo Request GET**
- Name: `Sales - General`
- URL: `http://localhost:8000/api/sales/byDepartment`
- Presiona Send ✈️

**2. Nuevo Request GET**
- Name: `Sales - By Store`
- URL: `http://localhost:8000/api/sales/byStore/5`
- Presiona Send ✈️

**3. Nuevo Request GET**
- Name: `Sales - Store with Dates`
- URL: `http://localhost:8000/api/sales/byStore/5/filtered`
- Params:
  - `start_date` = `2026-01-01`
  - `end_date` = `2026-01-31`
- Presiona Send ✈️

---

## Troubleshooting

### Error 1: "SQLSTATE[HY000]: General error"
```
Solución: Ejecuta php artisan migrate
```

### Error 2: "Call to undefined method..."
```
Solución: Verifica que los archivos están en las carpetas correctas
- Controllers/Sale/SalesController.php
- Repositories/Sale/SalesRepository.php
- etc.
```

### Error 3: "No hay datos disponibles"
```
Solución: Ejecuta php artisan db:seed --class=TransactionSeeder
para cargar datos de prueba
```

### Error 4: Rutas no aparecen en `route:list`
```
Solución: Verifica que routes/api.php contiene:
Route::prefix('sales')->group(function () {
    require __DIR__ . '/Sale/sales.php';
});
```

---

## Integración con Vue 3

### 1. Crear Composable

Crea archivo: `src/composables/useSales.ts`

```typescript
import { ref } from 'vue'

export function useSales() {
  const sales = ref([])
  const loading = ref(false)
  const error = ref(null)

  const getSalesByDepartment = async () => {
    try {
      loading.value = true
      const response = await fetch('http://localhost:8000/api/sales/byDepartment')
      const { data } = await response.json()
      sales.value = data
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  const getSalesByStore = async (storeId: number) => {
    try {
      loading.value = true
      const response = await fetch(`http://localhost:8000/api/sales/byStore/${storeId}`)
      const { data } = await response.json()
      sales.value = data
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

### 2. Usar en Componente Vue

Crea archivo: `src/views/SalesReport.vue`

```vue
<script setup lang="ts">
import { onMounted } from 'vue'
import { useSales } from '@/composables/useSales'

const { sales, loading, getSalesByDepartment } = useSales()

onMounted(() => {
  getSalesByDepartment()
})
</script>

<template>
  <div class="sales-report">
    <h1>Reporte de Ventas por Departamento</h1>
    
    <div v-if="loading" class="loading">
      ⏳ Cargando datos...
    </div>
    
    <table v-else class="sales-table">
      <thead>
        <tr>
          <th>Departamento</th>
          <th>Ventas Totales</th>
          <th>Transacciones</th>
          <th>Cantidad Vendida</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="sale in sales" :key="sale.id_department">
          <td>{{ sale.department }}</td>
          <td class="amount">${{ sale.total_sales.toFixed(2) }}</td>
          <td>{{ sale.total_transactions }}</td>
          <td>{{ sale.total_quantity.toFixed(2) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style scoped>
.sales-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
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

.amount {
  color: #27ae60;
  font-weight: bold;
}

.loading {
  text-align: center;
  padding: 2rem;
  font-size: 18px;
}
</style>
```

---

## Verificación de Integridad

### Checklist Completo:

```bash
# 1. Verificar que las migraciones se ejecutaron
php artisan migrate:status

# 2. Verificar que las tablas existen
php artisan tinker
>>> DB::table('transactions')->count()
>>> DB::table('transaction_details')->count()

# 3. Verificar que las rutas están registradas
php artisan route:list | grep sales

# 4. Verificar que los modelos cargan correctamente
php artisan tinker
>>> use App\Models\Transaction;
>>> Transaction::first();

# 5. Verificar que el controlador está accesible
php artisan tinker
>>> use App\Http\Controllers\Sale\SalesController;
>>> "OK"

# 6. Verificar que el repositorio funciona
php artisan tinker
>>> use App\Http\Repositories\Sale\SalesRepository;
>>> (new SalesRepository())->getSalesByDepartmentGeneral();
```

---

## Comandos Útiles para Desarrollo

### Ver logs en tiempo real:
```bash
tail -f storage/logs/laravel.log
```

### Limpiar cache:
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Reiniciar base de datos (⚠️ borra datos):
```bash
php artisan migrate:fresh --seed
```

### Abrir console interactiva:
```bash
php artisan tinker
```

---

## Endpoints en Resumen

| Endpoint | Comando curl |
|----------|--------------|
| Ventas generales | `curl http://localhost:8000/api/sales/byDepartment` |
| Ventas por tienda | `curl http://localhost:8000/api/sales/byStore/5` |
| Ventas con fechas | `curl "http://localhost:8000/api/sales/byStore/5/filtered?start_date=2026-01-01&end_date=2026-01-31"` |
| Transacciones depto | `curl http://localhost:8000/api/sales/transactions/department/1` |

---

## Prueba E2E (End-to-End)

```
1. Migrar BD              ✅ php artisan migrate
2. Crear datos            ✅ php artisan db:seed --class=TransactionSeeder
3. Verificar rutas        ✅ php artisan route:list | grep sales
4. Test curl              ✅ curl http://localhost:8000/api/sales/byDepartment
5. Verificar respuesta    ✅ Debe ser JSON válido con "success": true
6. Integrar Vue 3         ✅ Crear composable y componente
7. Probar en navegador    ✅ Tabla debe mostrar datos
```

---

## 📋 Archivo de Configuración (si necesitas auth)

En `app/Http/Middleware/Authenticate.php`:

```php
// Para proteger con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // ... rutas protegidas
});
```

O en `routes/Sale/sales.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/byDepartment', [SalesController::class, 'getSalesByDepartmentGeneral']);
    // ... más rutas
});
```

---

## ✨ Próximos Pasos (Recomendado)

1. ✅ Ejecutar migraciones
2. ✅ Cargar datos de prueba
3. ✅ Probar con curl/Postman
4. ✅ Crear composable en Vue
5. ✅ Crear página de dashboard
6. ✅ Agregar gráficos con Chart.js o similar
7. ✅ Implementar filtros por fechas en frontend
8. ✅ Agregar autenticación si es necesario
9. ✅ Crear tests unitarios
10. ✅ Documentar en Swagger/OpenAPI

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa `storage/logs/laravel.log`
2. Verifica que los archivos estén en las carpetas correctas
3. Ejecuta `composer dump-autoload`
4. Intenta `php artisan optimize`
5. Limpia caché con `php artisan cache:clear`

¡Listo para usar! 🚀

