# 🏗️ Diagrama de Arquitectura - Sales API

## Flujo de una Solicitud HTTP

```
┌──────────────────────────────────────────────────────────────────┐
│                      CLIENTE (Vue 3 / Postman)                   │
└────────────────────────────┬─────────────────────────────────────┘
                             │
                    GET /api/sales/byDepartment
                             │
┌────────────────────────────▼─────────────────────────────────────┐
│                    LARAVEL ROUTER (api.php)                      │
│                 Mapea la ruta al controlador                      │
└────────────────────────────┬─────────────────────────────────────┘
                             │
┌────────────────────────────▼─────────────────────────────────────┐
│              SalesController::getSalesByDepartmentGeneral()       │
│  ┌───────────────────────────────────────────────────────────┐   │
│  │ • Recibe la solicitud HTTP                                │   │
│  │ • Llama a SalesRepository                                 │   │
│  │ • Maneja excepciones y errores                            │   │
│  └───────────┬─────────────────────────────────────────────┘   │
└──────────────┼─────────────────────────────────────────────────┘
               │
┌──────────────▼─────────────────────────────────────────────────┐
│       SalesRepository::getSalesByDepartmentGeneral()            │
│ ┌────────────────────────────────────────────────────────────┐ │
│ │ • Construye la query SQL con Eloquent                      │ │
│ │ • Realiza JOINS entre tables:                              │ │
│ │   - transaction_details                                    │ │
│ │   - transactions                                           │ │
│ │   - departments                                            │ │
│ │   - general_deps                                           │ │
│ │ • Agrupa por departamento                                  │ │
│ │ • SUM(subtotal), COUNT(transactions), SUM(quantity)       │ │
│ │ • Ordena por ventas descendentes                           │ │
│ └────────────────┬─────────────────────────────────────────┘ │
└─────────────────┼───────────────────────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────────────────────┐
│                 BASE DE DATOS (PostgreSQL)                      │
│                                                                  │
│ SELECT                                                           │
│   d.id_department,                                              │
│   d.department,                                                 │
│   gd.g_departament as general_department,                      │
│   SUM(td.subtotal) as total_sales,                             │
│   COUNT(DISTINCT t.id_transaction) as total_transactions,      │
│   SUM(td.quantity) as total_quantity                           │
│ FROM transaction_details AS td                                 │
│ JOIN transactions AS t ON td.fk1_id_transaction = t.id_transaction
│ JOIN departments AS d ON td.fk2_id_department = d.id_department
│ JOIN general_deps AS gd ON d.fk1_id_general_dep = gd.id_general_dep
│ GROUP BY d.id_department, d.department, gd.g_departament      │
│ ORDER BY total_sales DESC                                      │
│                                                                  │
└─────────────────┬───────────────────────────────────────────────┘
                  │
                  │ Devuelve Colecciones
                  │
┌─────────────────▼───────────────────────────────────────────────┐
│      DepartmentSalesResource::collection($results)              │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Transforma cada fila en JSON:                            │   │
│  │ {                                                         │   │
│  │   "id_department": 1,                                    │   │
│  │   "department": "Electrónica",                           │   │
│  │   "general_department": "Tienda General",               │   │
│  │   "total_sales": 15000.50,                              │   │
│  │   "total_transactions": 25,                             │   │
│  │   "total_quantity": 150.00                              │   │
│  │ }                                                         │   │
│  └───────────────┬──────────────────────────────────────────┘   │
└──────────────────┼────────────────────────────────────────────────┘
                   │
┌──────────────────▼────────────────────────────────────────────────┐
│         UtilResponse::succesResponse() [Trait]                    │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │ Estandariza la respuesta JSON:                             │ │
│  │ {                                                           │ │
│  │   "success": true,                                         │ │
│  │   "message": "Ventas por departamento...",               │ │
│  │   "data": [ { ... }, { ... } ]                            │ │
│  │ }                                                           │ │
│  └────────────────┬────────────────────────────────────────────┘ │
└───────────────────┼──────────────────────────────────────────────┘
                    │
┌───────────────────▼──────────────────────────────────────────────┐
│                  HTTP Response 200 OK                            │
│              (Vuelve al cliente con datos)                       │
└────────────────────────────────────────────────────────────────┘
```

---

## 📦 Estructura de Componentes

```
                    ┌─────────────────────┐
                    │   Routes (api.php)  │
                    │  /api/sales/*       │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │ SalesController     │
                    │                     │
                    │ Methods:            │
                    │ • getSalesByDept()  │
                    │ • getSalesByStore() │
                    │ • getGeneral()      │
                    │ • getDates()        │
                    │ • getTransactions() │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │ SalesRepository     │
                    │                     │
                    │ Methods:            │
                    │ • getDeptGeneral()  │
                    │ • getDeptByStore()  │
                    │ • getGenDept()      │
                    │ • getDeptWithDates()
                    │ • getTransByDept()  │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │ Eloquent Models     │
                    │                     │
                    │ • Transaction       │
                    │ • TransactionDetail │
                    │ • Department        │
                    │ • Store             │
                    │ • User              │
                    └──────────┬──────────┘
                               │
                    ┌──────────▼──────────┐
                    │  Base de Datos      │
                    │                     │
                    │ • transactions      │
                    │ • transaction_det   │
                    │ • departments       │
                    │ • general_deps      │
                    │ • stores            │
                    │ • users             │
                    └─────────────────────┘
```

---

## 🔀 Flujo de Datos - Caso Específico: byStore

```
User hace solicitud:
GET /api/sales/byStore/5

         ↓

SalesController recibe {storeId: 5}

         ↓

Llama: $this->salesRepository->getSalesByDepartmentByStore(5)

         ↓

SalesRepository construye query:

    SELECT 
      d.id_department,
      d.department,
      gd.g_departament,
      s.id_store,
      s.store,
      SUM(td.subtotal) as total_sales,
      COUNT(DISTINCT t.id_transaction) as total_transactions,
      SUM(td.quantity) as total_quantity
    FROM transaction_details AS td
    JOIN transactions AS t ON ...
    JOIN departments AS d ON ...
    JOIN general_deps AS gd ON ...
    JOIN stores AS s ON ...
    WHERE t.fk1_id_store = 5   ← FILTRO CLAVE
    GROUP BY d.id_department, ...

         ↓

BD ejecuta y retorna:
[
  { id_department: 1, department: "Electrónica", ..., total_sales: 5000.25 },
  { id_department: 2, department: "Ropa", ..., total_sales: 3200.50 },
  ...
]

         ↓

DepartmentSalesResource->collection() transforma cada row

         ↓

UtilResponse->succesResponse() envuelve en JSON

         ↓

Respuesta HTTP 200:
{
  "success": true,
  "message": "...",
  "data": [
    { id_department: 1, department: "Electrónica", ..., total_sales: 5000.25 },
    { id_department: 2, department: "Ropa", ..., total_sales: 3200.50 }
  ]
}
```

---

## 🎭 Separación de Responsabilidades

```
┌─────────────────────────────────────────────────────────┐
│ RESPONSABILIDAD                  │ COMPONENTE            │
├─────────────────────────────────────────────────────────┤
│ Recibir solicitud HTTP            │ SalesController       │
│ Validar datos de entrada          │ SalesFilterRequest    │
│ Orquestar operaciones             │ SalesController       │
│ Construir queries                 │ SalesRepository       │
│ Agregar/filtrar datos             │ SalesRepository       │
│ Acceso a BD                        │ Eloquent Models       │
│ Transformar respuestas            │ DepartmentSalesRes.   │
│ Estandarizar respuestas           │ UtilResponse (Trait)  │
│ Enviar respuesta HTTP             │ Laravel Framework     │
└─────────────────────────────────────────────────────────┘
```

---

## 🗂️ Estructura de Carpetas Completa

```
backend/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Sale/
│   │   │   │   └── SalesController.php ..................... ✨ NUEVO
│   │   │   └── Api/
│   │   │       └── ... (controladores existentes)
│   │   │
│   │   ├── Repositories/
│   │   │   ├── Sale/
│   │   │   │   └── SalesRepository.php ..................... ✨ NUEVO
│   │   │   └── ... (repositorios existentes)
│   │   │
│   │   ├── Requests/
│   │   │   ├── Sale/
│   │   │   │   └── SalesFilterRequest.php .................. ✨ NUEVO
│   │   │   └── ... (requests existentes)
│   │   │
│   │   └── Resources/
│   │       ├── Sale/
│   │       │   ├── DepartmentSalesResource.php ............. ✨ NUEVO
│   │       │   ├── GeneralDepartmentSalesResource.php ...... ✨ NUEVO
│   │       │   └── TransactionResource.php ................. ✨ NUEVO
│   │       └── ... (resources existentes)
│   │
│   ├── Models/
│   │   ├── Transaction.php ................................ ✨ NUEVO
│   │   ├── TransactionDetail.php ........................... ✨ NUEVO
│   │   ├── Department.php .................................. ✏️  ACTUALIZADO
│   │   └── ... (modelos existentes)
│   │
│   ├── Traits/
│   │   └── UtilResponse.php ................................ (Existente)
│   │
│   └── Providers/
│       └── AppServiceProvider.php .......................... (Existente)
│
├── database/
│   ├── migrations/
│   │   ├── 2026_01_25_create_transactions_table.php ........ ✨ NUEVO
│   │   ├── 2026_01_25_create_transaction_details_table.php . ✨ NUEVO
│   │   └── ... (migraciones existentes)
│   │
│   └── seeders/
│       ├── TransactionSeeder.php ............................ ✨ NUEVO
│       └── DatabaseSeeder.php .............................. (Existente)
│
├── routes/
│   ├── Sale/
│   │   └── sales.php ....................................... ✨ NUEVO
│   ├── api.php ............................................. ✏️  ACTUALIZADO
│   ├── web.php ............................................. (Existente)
│   └── console.php ......................................... (Existente)
│
├── documentation/
│   ├── SALES_API.md ......................................... ✨ NUEVO
│   ├── SALES_API_QUICK_GUIDE.md ............................. ✨ NUEVO
│   ├── IMPLEMENTATION_SUMMARY.md ............................ ✨ NUEVO
│   ├── API_ARCHITECTURE.md .................................. ✨ ESTE ARCHIVO
│   └── API_AUTHENTICATION.md ................................ (Existente)
│
└── ... (otros archivos del proyecto)
```

---

## 🔗 Relaciones en Diagrama ER

```
                    ┌──────────────┐
                    │   USERS      │
                    │ (id_user)    │
                    └────┬─────────┘
                         │
                         │ 1 : Many
                         │
        ┌────────────────┴────────────────┐
        │                                 │
   ┌────▼──────────┐            ┌────────▼──────┐
   │ TRANSACTIONS  │            │ USER__STORES  │
   │(id_transaction)            │ (salary)      │
   │ • fk2_id_user │            └────────┬──────┘
   │ • fk1_id_store│                     │
   │ • total_amount│                     │ 1 : Many
   │ • trans_date  │                     │
   └────┬──────────┘            ┌────────▼──────┐
        │                       │    STORES     │
        │ 1 : Many             │ (id_store)    │
        │                       │ • colony      │
   ┌────▼───────────────────┐  │ • street      │
   │ TRANSACTION_DETAILS    │  └────┬──────────┘
   │(id_transaction_detail) │       │
   │ • quantity             │       │ 1 : Many
   │ • unit_price           │       │
   │ • subtotal             │  ┌────▼───────────┐
   └────┬────────────────────┘  │ GENERAL_DEPS  │
        │                       │(id_general_dep)
        │ Many : 1             │ • g_departament
        │                       └────┬───────────┘
   ┌────▼──────────────┐            │
   │   DEPARTMENTS     │ ◄──────────┘
   │(id_department)    │ 1 : Many
   │ • department      │
   │ • description     │
   └───────────────────┘
```

---

## 🔄 Ciclo de Vida de una Transacción

```
Step 1: Usuario hace una compra en Tienda Centro
┌─────────────────────────────────┐
│ INSERT INTO transactions        │
│ VALUES (                        │
│   store_id=5,                  │
│   user_id=10,                  │
│   total=500,                   │
│   transaction_date='2026-01-25'│
│ )                              │
│ → id_transaction = 1            │
└─────────────────────────────────┘

Step 2: Usuario compra 2 artículos de Electrónica
┌──────────────────────────────────┐
│ INSERT INTO transaction_details   │
│ VALUES (                          │
│   transaction_id=1,              │
│   department_id=1,               │
│   quantity=2,                    │
│   unit_price=250,                │
│   subtotal=500                   │
│ )                                │
└──────────────────────────────────┘

Step 3: API consulta '/api/sales/byStore/5'
┌───────────────────────────────────────────────────────┐
│ SELECT ... SUM(subtotal) FROM transaction_details     │
│ JOIN transactions ON ...                              │
│ JOIN departments ON ... = 1 (Electrónica)            │
│ WHERE transaction.store_id = 5                        │
│ GROUP BY departments.id                              │
│                                                       │
│ RESULTADO:                                            │
│ {                                                     │
│   "id_department": 1,                                │
│   "department": "Electrónica",                       │
│   "total_sales": 500,  ← SUMA ESTA VENTA             │
│   "total_transactions": ...,                         │
│   "total_quantity": ...                              │
│ }                                                     │
└───────────────────────────────────────────────────────┘
```

---

## 📊 Complejidad Algorítmica

| Operación | Complejidad | Notas |
|-----------|-------------|-------|
| `getSalesByDepartmentGeneral()` | O(n log n) | Agrupa y ordena toda la BD |
| `getSalesByDepartmentByStore()` | O(n log n) | Filtra + agrupa por tienda |
| `getTransactionsByDepartment()` | O(n) | Itera transacciones del depto |

**Optimización**: Los índices en `fk1_id_store` y `fk2_id_department` reducen el tiempo de búsqueda

---

## ✅ Checklist de Validación

- ✅ Todas las queries usan índices apropiados
- ✅ No hay N+1 problems (Eloquent con joins)
- ✅ Validación de entrada en FormRequest
- ✅ Manejo de excepciones en controlador
- ✅ Recursos transforman datos correctamente
- ✅ Respuestas HTTP con códigos apropiados
- ✅ Documentación en docblocks
- ✅ Seguimiento del patrón Repository
- ✅ Relaciones Eloquent bidireccionales
- ✅ Timestamps en tablas

