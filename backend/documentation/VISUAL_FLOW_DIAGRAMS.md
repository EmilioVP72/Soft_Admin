# 📊 Diagrama Visual de Flujos - APIs de Ventas

## 🔄 FLUJO 1: Obtener Ventas Generales por Departamento

```
┌─────────────────────────────────────────────────────────────────────────┐
│  Cliente solicita: GET /api/sales/byDepartment                          │
└────────────────────────────┬────────────────────────────────────────────┘
                             │
                             ▼
                    ┌─────────────────────┐
                    │ Router (api.php)    │
                    │ Distribuye a:       │
                    │ SalesController     │
                    └────────────┬────────┘
                                 │
                                 ▼
                 ┌───────────────────────────────┐
                 │ SalesController::             │
                 │ getSalesByDepartmentGeneral() │
                 │                               │
                 │ ✓ Recibe GET request         │
                 │ ✓ Llama al repositorio       │
                 │ ✓ Maneja errores             │
                 └───────────────┬───────────────┘
                                 │
                                 ▼
              ┌──────────────────────────────────────┐
              │ SalesRepository::                    │
              │ getSalesByDepartmentGeneral()        │
              │                                      │
              │ Query:                               │
              │ ├─ SELECT id_department              │
              │ ├─ SELECT department                 │
              │ ├─ SUM(subtotal) as total_sales     │
              │ ├─ COUNT(transactions)               │
              │ ├─ SUM(quantity)                     │
              │ ├─ FROM transaction_details          │
              │ ├─ JOIN transactions                 │
              │ ├─ JOIN departments                  │
              │ ├─ JOIN general_deps                 │
              │ ├─ GROUP BY department               │
              │ └─ ORDER BY total_sales DESC         │
              └───────────────┬────────────────────┘
                              │
                              ▼
              ┌──────────────────────────────────────┐
              │ BASE DE DATOS                        │
              │ PostgreSQL / MySQL                   │
              │                                      │
              │ ✓ Ejecuta query                      │
              │ ✓ Agrupa por departamento            │
              │ ✓ Calcula sumas y conteos            │
              │ ✓ Retorna Collection                 │
              └───────────────┬────────────────────┘
                              │
                              ▼
            ┌────────────────────────────────────────┐
            │ DepartmentSalesResource::collection()  │
            │                                        │
            │ Transforma cada fila:                  │
            │ {                                      │
            │   "id_department": 1,                 │
            │   "department": "Electrónica",        │
            │   "general_department": "...",        │
            │   "total_sales": 15000.50,            │
            │   "total_transactions": 25,           │
            │   "total_quantity": 150.00            │
            │ }                                      │
            └────────────────┬─────────────────────┘
                             │
                             ▼
            ┌──────────────────────────────────────┐
            │ UtilResponse::succesResponse()        │
            │ (Trait)                               │
            │                                      │
            │ Envuelve en estructura estándar:     │
            │ {                                    │
            │   "success": true,                   │
            │   "message": "...",                  │
            │   "data": [ ... ]                    │
            │ }                                    │
            └────────────────┬────────────────────┘
                             │
                             ▼
            ┌──────────────────────────────────────┐
            │ HTTP Response 200 OK                 │
            │ Content-Type: application/json       │
            │                                      │
            │ Vuelve al cliente con datos          │
            └────────────────┬────────────────────┘
                             │
                             ▼
        ┌────────────────────────────────────────────┐
        │ Cliente recibe JSON con todos los          │
        │ departamentos y sus ventas totales         │
        │                                            │
        │ Renderiza tabla/gráfico en Vue            │
        └────────────────────────────────────────────┘
```

---

## 🏪 FLUJO 2: Obtener Ventas de una Tienda Específica

```
┌──────────────────────────────────────────────────────────────┐
│ Cliente solicita: GET /api/sales/byStore/5                  │
│                                                              │
│ Parámetro: storeId = 5 (ID de la tienda)                   │
└────────────────────────┬─────────────────────────────────────┘
                         │
                         ▼
              ┌──────────────────────┐
              │ Router (api.php)     │
              │ Extrae parámetro: 5  │
              │ Enruta a:            │
              │ SalesController      │
              └────────────┬─────────┘
                           │
                           ▼
          ┌────────────────────────────────────┐
          │ SalesController::                  │
          │ getSalesByDepartmentByStore(5)     │
          │                                    │
          │ ✓ Valida que tienda existe         │
          │ ✓ Pasa storeId al repositorio     │
          │ ✓ Maneja errores                   │
          └────────────┬───────────────────────┘
                       │
                       ▼
       ┌──────────────────────────────────────────────┐
       │ SalesRepository::                            │
       │ getSalesByDepartmentByStore(5)               │
       │                                              │
       │ Construye query:                             │
       │ ├─ SELECT id_department                      │
       │ ├─ SELECT department                         │
       │ ├─ SELECT id_store, store (✓ NUEVO)         │
       │ ├─ SUM(subtotal) as total_sales             │
       │ ├─ FROM transaction_details                  │
       │ ├─ JOIN transactions                         │
       │ ├─ JOIN departments                          │
       │ ├─ JOIN general_deps                         │
       │ ├─ JOIN stores                               │
       │ ├─ WHERE t.fk1_id_store = 5 (✓ FILTRO)      │
       │ ├─ GROUP BY department                       │
       │ └─ ORDER BY total_sales DESC                 │
       └───────────────┬──────────────────────────────┘
                       │
                       ▼
       ┌──────────────────────────────────────────────┐
       │ BASE DE DATOS                                │
       │                                              │
       │ ✓ Busca transacciones de tienda 5            │
       │ ✓ Agrupa por departamento                    │
       │ ✓ Calcula totales solo de esa tienda         │
       │ ✓ Retorna resultados filtrados               │
       └───────────────┬──────────────────────────────┘
                       │
                       ▼
       ┌──────────────────────────────────────────────┐
       │ DepartmentSalesResource::collection()        │
       │                                              │
       │ Transforma con id_store y store:            │
       │ {                                            │
       │   "id_department": 1,                       │
       │   "department": "Electrónica",              │
       │   "id_store": 5,        (✓ NUEVO)           │
       │   "store": "Tienda Centro", (✓ NUEVO)       │
       │   "total_sales": 5000.25,                   │
       │   "total_transactions": 10,                 │
       │   "total_quantity": 50.00                   │
       │ }                                            │
       └───────────────┬──────────────────────────────┘
                       │
                       ▼
       ┌──────────────────────────────────────────────┐
       │ UtilResponse::succesResponse()               │
       │                                              │
       │ JSON Estándar con solo datos de tienda 5    │
       └───────────────┬──────────────────────────────┘
                       │
                       ▼
       ┌──────────────────────────────────────────────┐
       │ HTTP Response 200 OK                         │
       │                                              │
       │ Cliente recibe datos de Tienda Centro        │
       │ Con ventas desglosadas por departamento      │
       └──────────────────────────────────────────────┘
```

---

## 📅 FLUJO 3: Ventas con Filtro de Fechas

```
┌────────────────────────────────────────────────────────────────┐
│ Cliente solicita:                                              │
│ GET /api/sales/byStore/5/filtered?start_date=2026-01-01      │
│                           &end_date=2026-01-31                │
│                                                                │
│ Parámetros:                                                    │
│ • storeId: 5                                                   │
│ • start_date: 2026-01-01                                       │
│ • end_date: 2026-01-31                                         │
└─────────────────────┬──────────────────────────────────────────┘
                      │
                      ▼
         ┌────────────────────────────┐
         │ Router (api.php)           │
         │ Extrae parámetros          │
         └────────────┬───────────────┘
                      │
                      ▼
      ┌────────────────────────────────────────┐
      │ SalesFilterRequest                     │
      │ (Validación de entrada)                │
      │                                        │
      │ Valida:                                │
      │ ✓ store_id es integer                  │
      │ ✓ store_id existe en BD                │
      │ ✓ start_date es fecha válida          │
      │ ✓ end_date es fecha válida            │
      │ ✓ start_date <= end_date               │
      │                                        │
      │ Si hay error → 422 Unprocessable       │
      │ Si todo OK → continúa                  │
      └────────────┬─────────────────────────┘
                   │
                   ▼
      ┌──────────────────────────────────────────┐
      │ SalesController::                        │
      │ getSalesByDepartmentStoreWithDates(5)    │
      │                                          │
      │ Pasa parámetros validados al repositorio │
      └────────────┬─────────────────────────────┘
                   │
                   ▼
      ┌──────────────────────────────────────────────────────┐
      │ SalesRepository::                                    │
      │ getSalesByDepartmentStoreWithDateRange(5, ...)      │
      │                                                      │
      │ Construye query DINÁMICA:                           │
      │ ├─ Base SQL (JOIN, GROUP, SELECT)                   │
      │ ├─ IF start_date:                                   │
      │ │  WHERE transaction_date >= '2026-01-01'          │
      │ │                                                   │
      │ ├─ IF end_date:                                     │
      │ │  WHERE transaction_date <= '2026-01-31'          │
      │ │                                                   │
      │ └─ WHERE fk1_id_store = 5                           │
      └────────────┬──────────────────────────────────────┘
                   │
                   ▼
      ┌──────────────────────────────────────────────────┐
      │ BASE DE DATOS                                    │
      │                                                  │
      │ WHERE transaction_date >= '2026-01-01'          │
      │ AND   transaction_date <= '2026-01-31'          │
      │ AND   fk1_id_store = 5                          │
      │                                                  │
      │ ✓ Busca solo transacciones en enero             │
      │ ✓ De la tienda 5                                │
      │ ✓ Agrupa por departamento                       │
      │ ✓ Retorna resultados filtrados                  │
      └────────────┬──────────────────────────────────┘
                   │
                   ▼
      ┌──────────────────────────────────────────────────┐
      │ Resultado ejemplo:                               │
      │                                                  │
      │ [                                                │
      │   {                                              │
      │     "id_department": 1,                          │
      │     "department": "Electrónica",                │
      │     "total_sales": 2000.00,     (solo enero)    │
      │     "total_transactions": 5,                     │
      │     "total_quantity": 25.00                     │
      │   },                                             │
      │   {                                              │
      │     "id_department": 2,                          │
      │     "department": "Ropa",                       │
      │     "total_sales": 1500.00,     (solo enero)    │
      │     "total_transactions": 8,                     │
      │     "total_quantity": 60.00                     │
      │   }                                              │
      │ ]                                                │
      └────────────┬──────────────────────────────────┘
                   │
                   ▼
      ┌──────────────────────────────────────────────────┐
      │ HTTP Response 200 OK                             │
      │                                                  │
      │ Cliente recibe SOLO datos de enero               │
      │ de la tienda 5                                   │
      └──────────────────────────────────────────────────┘
```

---

## 🔀 FLUJO 4: Comparación de Resultados

```
MISMO ENDPOINT: GET /api/sales/byStore/5/filtered

┌─────────────────────────────────────────────────────────────────┐
│  SIN PARÁMETROS DE FECHA          │  CON RANGO DE FECHAS        │
├─────────────────────────────────────────────────────────────────┤
│  GET /api/sales/byStore/5         │  GET /api/sales/byStore/5/  │
│                                   │  filtered?start_date=2026-01 │
│                                   │              &end_date=2026-01-31
│                                   │                             │
│  SELECT * FROM transactions       │  SELECT * FROM transactions │
│  WHERE store_id = 5               │  WHERE store_id = 5         │
│  (todos los tiempos)              │  AND date >= '2026-01-01'   │
│                                   │  AND date <= '2026-01-31'   │
│                                   │  (solo enero)               │
│                                   │                             │
│  Resultado:                       │  Resultado:                 │
│  Electrónica: $15,000             │  Electrónica: $2,000        │
│  Ropa: $8,500                     │  Ropa: $1,500               │
│  TOTAL: $23,500                   │  TOTAL: $3,500              │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Estadísticas de Datos

```
FLUJO DE DATOS EN BASE DE DATOS:

Step 1: Usuario compra en tienda
┌─────────────────┐
│  transactions   │  (1 registro)
│  ID: 1          │
│  Store: 5       │
│  Total: $500    │
│  Date: 2026-01-15
└────────┬────────┘
         │
         │ 1 transacción puede tener
         │ múltiples detalles
         │
Step 2: Detalles de compra
┌──────────────────────┐
│ transaction_details  │  (2 registros)
├──────────────────────┤
│ detail 1:            │  (2 unidades de Electrónica)
│ Dept: 1              │
│ Qty: 2               │
│ Unit Price: 250      │
│ Subtotal: $500       │
├──────────────────────┤
│ detail 2:            │  (Electrónica otra vez)
│ (si hubiera)         │
│ Dept: 2              │
│ ...                  │
└────────┬─────────────┘
         │
         │ Agregación: SUM(subtotal)
         │            COUNT(transactions)
         │            SUM(quantity)
         │
Step 3: Resultado agregado
┌──────────────────────┐
│ Electrónica          │
│ Total Sales: $500    │
│ Transactions: 1      │
│ Quantity: 2          │
└──────────────────────┘
```

---

## 🎯 Matriz de Decisión de Endpoints

```
┌────────────────────────────────────────────────────────────────────┐
│  NECESIDAD DEL USUARIO          │  ENDPOINT A USAR                 │
├────────────────────────────────────────────────────────────────────┤
│  "Ver TODAS las ventas de       │  GET /api/sales/byDepartment     │
│   cada depto globalmente"       │                                  │
├────────────────────────────────────────────────────────────────────┤
│  "Ver ventas de mi tienda por   │  GET /api/sales/byStore/5        │
│   departamento"                 │                                  │
├────────────────────────────────────────────────────────────────────┤
│  "Reporte mensual de una tienda │  GET /api/sales/byStore/5/       │
│   específica"                   │  filtered?start_date=...&       │
│                                 │  end_date=...                   │
├────────────────────────────────────────────────────────────────────┤
│  "Auditar todas las transacciones │  GET /api/sales/transactions/  │
│   de un departamento"             │  department/1                  │
├────────────────────────────────────────────────────────────────────┤
│  "Comparar depto general         │  GET /api/sales/byGeneralDept  │
│   vs específicos"                │                                  │
└────────────────────────────────────────────────────────────────────┘
```

---

## 🚨 Manejo de Errores

```
Cliente envía Request
    │
    ▼
┌──────────────────────┐
│ Validar Parámetros   │
│ (SalesFilterRequest) │
└─────────┬────────────┘
          │
    ┌─────┴──────┐
    │            │
    ▼            ▼
  ERROR       OK
   │          │
   ▼          ▼
   │    SalesController
   │         │
   │    ┌────▼─────┐
   │    │ Buscar   │
   │    │ Datos    │
   │    └────┬─────┘
   │         │
   │    ┌────┴──────────┐
   │    │              │
   │    ▼              ▼
   │  ENCONTRADO    NO ENCONTRADO
   │    │               │
   │    ▼               ▼
   │  Transform       Return 404
   │  Resource        │
   │    │             │
   │    └─────┬───────┘
   │          │
   │          ▼
   └────────►Response

Códigos posibles:
✓ 200 - Éxito
✗ 404 - No encontrado
✗ 422 - Validación fallida
✗ 500 - Error del servidor
```

---

## ✨ Resumen Visual de Capacidades

```
API DE VENTAS
│
├─ REPORTES
│  ├─ Total por departamento (todas tiendas)
│  ├─ Total por departamento (una tienda)
│  ├─ Total por depto general
│  └─ Total con filtro de fechas
│
├─ FILTROS
│  ├─ Por tienda
│  ├─ Por rango de fechas
│  ├─ Por departamento
│  └─ Por departamento general
│
├─ DATOS
│  ├─ Total de ventas ($)
│  ├─ Cantidad de transacciones
│  ├─ Cantidad de unidades
│  └─ Detalles de transacciones
│
└─ RESPUESTAS
   ├─ JSON estructurado
   ├─ Mensajes descriptivos
   ├─ Códigos HTTP apropiados
   └─ Errores detallados
```

