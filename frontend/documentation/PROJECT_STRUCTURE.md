# Estructura del Proyecto Frontend

Este documento describe la organización completa de archivos y carpetas del proyecto frontend.

## Árbol de Directorios

```
frontend/
├── documentation/              # Documentación del proyecto
│   ├── README.md
│   ├── SETUP_INSTALLATION.md
│   ├── DEPENDENCIES.md
│   ├── PROJECT_STRUCTURE.md
│   ├── ARCHITECTURE.md
│   ├── COMPONENTS.md
│   ├── STORES_STATE.md
│   ├── ROUTING.md
│   ├── AUTHENTICATION.md
│   ├── API_INTEGRATION.md
│   ├── CHARTS_GRAPHICS.md
│   └── STYLING.md
├── public/                     # Archivos estáticos
│   └── (assets estáticos que no requieren processing)
├── src/                        # Código fuente
│   ├── api/                    # Configuración de clientes HTTP
│   │   ├── axios.ts           # Cliente principal con interceptores
│   │   └── stores.ts          # Cliente para endpoints de tiendas
│   ├── assets/                # Assets que requieren processing
│   │   ├── images/            # Imágenes
│   │   └── styles/            # Estilos CSS
│   │       ├── auth/
│   │       │   └── login/
│   │       │       └── login.css
│   │       ├── base/
│   │       │   ├── navbar.css
│   │       │   └── SessionModal.css
│   │       └── dashboard/
│   │           ├── dashboard.css
│   │           └── componentes/
│   │               ├── fastActionsDashboard.css
│   │               ├── graphics.css
│   │               └── report.css
│   ├── components/            # Componentes reutilizables
│   │   ├── dashboard/
│   │   │   ├── FastActionDashboard.vue
│   │   │   ├── Graphics.vue
│   │   │   ├── Report.vue
│   │   │   └── graphics/
│   │   │       └── BarGraph.vue
│   │   ├── Navbar.vue
│   │   └── SessionModal.vue
│   ├── composables/           # Composables de Vue (lógica reutilizable)
│   │   └── useSessionWarning.ts
│   ├── interfaces/            # Definiciones de tipos TypeScript
│   │   ├── AuthInterfaces.ts
│   │   └── StoresInterfaces.ts
│   ├── router/                # Configuración de Vue Router
│   │   └── index.ts
│   ├── services/              # Servicios de API (capa de abstracción)
│   │   ├── LoginServices.ts
│   │   └── StoresServices.ts
│   ├── stores/                # Pinia stores (gestión de estado)
│   │   └── auth.ts
│   ├── views/                 # Componentes de vista (páginas)
│   │   ├── auth/
│   │   │   └── LoginView.vue
│   │   └── dashboard/
│   │       └── DashboardView.vue
│   ├── App.vue                # Componente raíz
│   ├── main.ts                # Entry point de la aplicación
│   └── style.css              # Estilos globales
├── .dockerignore              # Archivos ignorados por Docker
├── .env                       # Variables de entorno (local, no en git)
├── .env.production            # Variables de entorno de producción
├── .gitignore                 # Archivos ignorados por Git
├── Dockerfile                 # Configuración de contenedor Docker
├── index.html                 # HTML template
├── package.json               # Dependencias y scripts npm
├── package-lock.json          # Lock file de npm
├── README.md                  # README del proyecto
├── tsconfig.json              # Configuración TypeScript raíz
├── tsconfig.app.json          # Configuración TypeScript para la app
├── tsconfig.node.json         # Configuración TypeScript para Node
└── vite.config.ts             # Configuración de Vite
```

## Descripción de Directorios Principales

### `/documentation`

Contiene toda la documentación técnica del proyecto.

**Propósito**: Centralizar información para desarrolladores nuevos y referencia rápida.

**Archivos**:
- `README.md`: Índice general
- `SETUP_INSTALLATION.md`: Guía de instalación
- `DEPENDENCIES.md`: Lista de dependencias
- Y otros documentos específicos por tema

### `/public`

Archivos estáticos servidos directamente sin procesamiento de Vite.

**Uso**: Assets que no necesitan ser importados en el código JavaScript.

**Ejemplos**: favicon, robots.txt, imágenes que no cambian

### `/src`

**Corazón de la aplicación**. Todo el código fuente ejecutable.

#### `/src/api`

Configuración de clientes HTTP Axios.

**Archivos**:
- `axios.ts`: Cliente principal con interceptores de autenticación
- `stores.ts`: Cliente específico para API de stores (sin autenticación)

**Responsabilidad**: Configurar instancias de Axios, headers, interceptores

#### `/src/assets`

Assets que pasan por el pipeline de build de Vite.

**Subcarpetas**:
- `images/`: Imágenes optimizadas por Vite
- `styles/`: CSS modular organizado por feature

**Organización de estilos**:
```
styles/
├── auth/           # Estilos de autenticación
├── base/           # Componentes base (navbar, modales)
└── dashboard/      # Estilos del dashboard
    └── componentes/
```

#### `/src/components`

Componentes Vue reutilizables.

**Estructura**:
```
components/
├── dashboard/              # Componentes específicos del dashboard
│   ├── FastActionDashboard.vue
│   ├── Graphics.vue
│   ├── Report.vue
│   └── graphics/          # Sub-componentes
│       └── BarGraph.vue
├── Navbar.vue             # Componentes globales
└── SessionModal.vue
```

**Principio**: Componentes pequeños, reutilizables, single-responsibility

#### `/src/composables`

Funciones composables (Composition API).

**Archivos**:
- `useSessionWarning.ts`: Lógica de advertencia de sesión expirada

**Propósito**: Extraer lógica reutilizable independiente de componentes

**Patrón**: Funciones que retornan refs reactivos y funciones

#### `/src/interfaces`

Definiciones de tipos e interfaces TypeScript.

**Archivos**:
- `AuthInterfaces.ts`: Tipos para autenticación
- `StoresInterfaces.ts`: Tipos para stores/sucursales

**Convención**: Un archivo por dominio/feature

#### `/src/router`

Configuración de Vue Router.

**Archivo principal**: `index.ts`

**Contiene**:
- Definición de rutas
- Guards de navegación
- Lazy loading de vistas
- Meta fields

#### `/src/services`

Capa de abstracción para llamadas a la API.

**Archivos**:
- `LoginServices.ts`: Servicios de autenticación
- `StoresServices.ts`: Servicios de tiendas/ventas

**Patrón**: 
```typescript
export default {
  metodo(params) {
    return apiClient.method(url, params);
  }
}
```

**Ventajas**: 
- Desacopla componentes de la implementación de HTTP
- Fácil testing con mocks
- Punto único para modificar endpoints

#### `/src/stores`

Pinia stores para gestión de estado global.

**Archivos**:
- `auth.ts`: Estado de autenticación y tokens

**Patrón**: Composition API stores con `defineStore`

#### `/src/views`

Componentes de vista que representan páginas completas.

**Estructura**:
```
views/
├── auth/
│   └── LoginView.vue      # Página de login
└── dashboard/
    └── DashboardView.vue  # Página del dashboard
```

**Diferencia con components**: Views son páginas completas ligadas a rutas

#### Archivos Raíz de `/src`

- **`App.vue`**: Componente raíz, contiene router-view y estructura global
- **`main.ts`**: Entry point, inicializa Vue, Pinia, Router
- **`style.css`**: Estilos globales (resets, variables CSS)

## Archivos de Configuración Raíz

### `package.json`
Manifiesto del proyecto npm: dependencias, scripts, metadata

### `package-lock.json`
Lock file para reproducibilidad de instalaciones

### `tsconfig.json`
Configuración raíz de TypeScript (referencias a otros configs)

### `tsconfig.app.json`
Configuración TypeScript para código de aplicación

### `tsconfig.node.json`
Configuración TypeScript para scripts de Node (vite.config.ts)

### `vite.config.ts`
Configuración de Vite: plugins, server, aliases, build

### `Dockerfile`
Configuración de contenedor Docker para desarrollo

### `.env` y `.env.production`
Variables de entorno para diferentes ambientes

### `.gitignore`
Archivos y carpetas ignorados por Git

### `.dockerignore`
Archivos ignorados al construir imagen Docker

### `index.html`
Template HTML principal (Vite lo procesa)

## Convenciones de Nombres

### Archivos

- **Componentes Vue**: `PascalCase.vue` (ej: `Navbar.vue`, `SessionModal.vue`)
- **TypeScript**: `camelCase.ts` o `PascalCase.ts` según el contenido
  - Services: `PascalCase.ts` (ej: `LoginServices.ts`)
  - Composables: `camelCase.ts` con prefijo `use` (ej: `useSessionWarning.ts`)
  - Stores: `camelCase.ts` (ej: `auth.ts`)
  - Interfaces: `PascalCase.ts` (ej: `AuthInterfaces.ts`)
- **CSS**: `kebab-case.css` o nombre del componente (ej: `navbar.css`)

### Carpetas

- **kebab-case**: Para la mayoría de carpetas (ej: `components/`, `api/`)
- **PascalCase**: Cuando agrupa componentes relacionados (opcional)

### Imports

Usando alias `@` para imports absolutos:

```typescript
// ✅ Recomendado
import { useAuthStore } from '@/stores/auth'
import LoginServices from '@/services/LoginServices'

// ❌ Evitar
import { useAuthStore } from '../../../stores/auth'
```

## Flujo de Datos

```
main.ts
  └── App.vue
       ├── Router
       │    └── Views
       │         └── Components
       │              └── Child Components
       └── Stores (Pinia)
            └── Shared State

Services → API Clients → Backend
```

## Patrones de Organización

### Por Feature/Dominio

Cuando una feature crece mucho, se organiza en su propia carpeta:

```
components/dashboard/
  ├── FastActionDashboard.vue
  ├── Graphics.vue
  └── graphics/
      └── BarGraph.vue
```

### Separación de Concerns

- **Lógica de negocio**: Services + Composables
- **Estado global**: Stores
- **UI**: Components + Views
- **Tipos**: Interfaces
- **Configuración HTTP**: API clients
- **Estilos**: Assets/styles organizado por feature

### Lazy Loading

Las vistas se cargan de forma lazy:

```typescript
component: () => import('@/views/auth/LoginView.vue')
```

**Beneficios**: Code splitting, mejor performance inicial

## Escalabilidad

La estructura actual soporta crecimiento en:

1. **Más features**: Agregar carpetas en `views/`, `components/`, `services/`
2. **Más stores**: Crear archivos en `stores/` por dominio
3. **Más composables**: Extraer lógica reutilizable a `composables/`
4. **Más tipos**: Ampliar `interfaces/` por feature

### Ejemplo de Crecimiento

Si se agrega un módulo de "Inventario":

```
src/
├── views/inventory/
│   └── InventoryView.vue
├── components/inventory/
│   ├── ProductList.vue
│   └── ProductForm.vue
├── services/
│   └── InventoryServices.ts
├── stores/
│   └── inventory.ts
└── interfaces/
    └── InventoryInterfaces.ts
```

## Tamaños Aproximados

- **Total archivos**: ~30 archivos de código fuente
- **Componentes Vue**: ~9 archivos .vue
- **TypeScript**: ~10 archivos .ts
- **CSS**: ~7 archivos .css
- **Configuración**: ~6 archivos de config

## Checklist de Estructura

Al agregar nuevas features, considera:

- [ ] ¿La vista va en `/views` con lazy loading?
- [ ] ¿Los componentes reutilizables van en `/components`?
- [ ] ¿Los tipos están definidos en `/interfaces`?
- [ ] ¿La lógica de API está en `/services`?
- [ ] ¿El estado global usa Pinia en `/stores`?
- [ ] ¿La lógica reutilizable está en `/composables`?
- [ ] ¿Los estilos están organizados por feature en `/assets/styles`?
- [ ] ¿Se usan imports absolutos con `@/`?

## Referencias

- Estructura basada en [Vue 3 Official Guide](https://vuejs.org/guide/scaling-up/sfc.html)
- Inspirada en [Vue Router Best Practices](https://router.vuejs.org/guide/advanced/navigation-guards.html)
- Convenciones de [Vue Style Guide](https://vuejs.org/style-guide/)
