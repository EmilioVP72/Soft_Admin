# Dependencias del Proyecto

Este documento lista todas las dependencias del proyecto, su propósito y versión instalada.

## Dependencias de Producción

Estas librerías son necesarias para que la aplicación funcione en producción.

### Vue 3 Ecosystem

#### **vue** `^3.5.24`
- **Propósito**: Framework JavaScript progresivo para construir interfaces de usuario
- **Uso**: Core del proyecto, toda la aplicación está construida con Vue 3
- **Características utilizadas**: 
  - Composition API
  - Reactive system
  - Template syntax
  - Component system

#### **vue-router** `^4.6.4`
- **Propósito**: Router oficial de Vue.js
- **Uso**: Gestión de rutas y navegación entre vistas
- **Características utilizadas**:
  - Route guards (beforeEach)
  - Lazy loading de componentes
  - Meta fields para control de acceso
  - History mode (HTML5)

#### **pinia** `^3.0.4`
- **Propósito**: State management oficial de Vue 3
- **Uso**: Gestión de estado global de la aplicación
- **Stores implementados**:
  - `auth.ts`: Gestión de autenticación y tokens
- **Ventajas sobre Vuex**: Más simple, mejor TypeScript support, sin mutaciones

### HTTP Client

#### **axios** `^1.13.4`
- **Propósito**: Cliente HTTP basado en promesas
- **Uso**: Comunicación con la API backend
- **Características utilizadas**:
  - Interceptores de request/response
  - Configuración de baseURL
  - Headers automáticos (Authorization)
  - Manejo de errores centralizado

### Gráficas y Visualización de Datos

#### **chart.js** `^4.5.1`
- **Propósito**: Librería de gráficas JavaScript
- **Uso**: Renderizado de gráficas de barras para ventas
- **Tipos de gráficas utilizadas**: Bar charts
- **Plugins registrados**: 
  - Title
  - Tooltip
  - Legend
  - BarElement
  - CategoryScale
  - LinearScale

#### **vue-chartjs** `^5.3.3`
- **Propósito**: Wrapper de Vue para Chart.js
- **Uso**: Integración de Chart.js con componentes Vue
- **Componentes utilizados**: `<Bar />`
- **Ventajas**: Reactive updates, fácil integración con Vue

### Iconos

#### **@heroicons/vue** `^2.2.0`
- **Propósito**: Iconos SVG diseñados por Tailwind CSS
- **Uso**: Iconografía en toda la aplicación
- **Variantes utilizadas**: 
  - Outline (24x24) para botones y acciones
- **Iconos específicos**:
  - `DocumentCurrencyDollarIcon`
  - `BanknotesIcon`
  - `TableCellsIcon`
  - `PresentationChartLineIcon`
  - `BuildingStorefrontIcon`
  - `UserCircleIcon`

### Validación

#### **zod** `^4.3.6`
- **Propósito**: Schema validation library con TypeScript-first
- **Uso**: Validación de formularios y datos
- **Implementaciones**:
  - Validación de login (email y password)
- **Ventajas**: 
  - Type inference automático
  - Mensajes de error personalizados
  - Composición de schemas

## Dependencias de Desarrollo

Estas dependencias solo se usan durante el desarrollo y no se incluyen en el bundle de producción.

### Build Tools

#### **vite** `^7.2.4`
- **Propósito**: Build tool y dev server de próxima generación
- **Uso**: Bundling, dev server, HMR (Hot Module Replacement)
- **Configuraciones aplicadas**:
  - Server en host `0.0.0.0` para acceso en red
  - Puerto 5173
  - Polling activado para Docker
  - Alias `@` para `/src`
- **Ventajas**:
  - Inicio instantáneo del servidor
  - HMR ultra rápido
  - Build optimizado con Rollup

#### **@vitejs/plugin-vue** `^6.0.1`
- **Propósito**: Plugin oficial de Vite para Vue 3
- **Uso**: Compilación de archivos `.vue`
- **Características**: 
  - Hot reload de componentes
  - SFC (Single File Components) support

### TypeScript

#### **typescript** `~5.9.3`
- **Propósito**: Superset de JavaScript con tipado estático
- **Uso**: Type safety en todo el proyecto
- **Configuración**: Ver `tsconfig.json`, `tsconfig.app.json`, `tsconfig.node.json`
- **Beneficios**:
  - Autocompletado mejorado
  - Detección temprana de errores
  - Mejor refactoring

#### **vue-tsc** `^3.1.4`
- **Propósito**: Type-check para archivos Vue usando TypeScript
- **Uso**: Compilación y verificación de tipos en build
- **Comando**: `npm run build` ejecuta `vue-tsc -b`

#### **@vue/tsconfig** `^0.8.1`
- **Propósito**: Configuraciones de TypeScript recomendadas para Vue
- **Uso**: Base para las configuraciones de tsconfig

#### **@types/node** `^24.10.1`
- **Propósito**: Definiciones de tipos para Node.js
- **Uso**: Type support para APIs de Node.js y path aliases

## Versiones y Compatibilidad

### Node.js
- **Versión requerida**: >= 23.7.0
- **Recomendada**: 23.7.0 (Alpine 3.20 en Docker)

### npm
- **Versión requerida**: >= 10.x
- **Lockfile**: `package-lock.json` (commit al repo)

## Scripts Disponibles

```json
{
  "dev": "vite --host 0.0.0.0 --port 5173",
  "build": "vue-tsc -b && vite build",
  "preview": "vite preview"
}
```

### `npm run dev`
- Inicia el servidor de desarrollo
- Hot Module Replacement habilitado
- Accesible desde la red local
- Puerto: 5173

### `npm run build`
- Type-check con `vue-tsc`
- Build de producción con Vite
- Output: carpeta `dist/`
- Minificación y tree-shaking

### `npm run preview`
- Previsualiza el build de producción
- Útil para testing antes de deploy

## Instalación de Dependencias

### Instalar todas las dependencias
```bash
npm install
```

### Instalar una nueva dependencia de producción
```bash
npm install <paquete>
```

### Instalar una nueva dependencia de desarrollo
```bash
npm install -D <paquete>
```

### Actualizar dependencias
```bash
# Ver dependencias desactualizadas
npm outdated

# Actualizar a versiones compatibles
npm update

# Actualizar a latest (cuidado con breaking changes)
npm install <paquete>@latest
```

## Auditoría de Seguridad

```bash
# Ver vulnerabilidades
npm audit

# Intentar arreglar automáticamente
npm audit fix

# Forzar fixes (puede romper compatibilidad)
npm audit fix --force
```

## Bundle Size

Para analizar el tamaño del bundle:

```bash
npm run build
# Revisar el output en consola para ver tamaños

# O usar vite-plugin-visualizer (no instalado actualmente)
npm install -D rollup-plugin-visualizer
```

## Consideraciones Importantes

### Compatibilidad de Versiones

1. **Vue 3.5.x + Vue Router 4.6.x + Pinia 3.0.x**: Totalmente compatible
2. **Vite 7.x + Vue 3.5.x**: Configuración optimizada
3. **Chart.js 4.x + vue-chartjs 5.x**: Compatible con Vue 3
4. **TypeScript 5.9.x**: Excelente soporte para Vue 3

### Peer Dependencies

Todas las peer dependencies están correctamente instaladas:
- Vue Router requiere Vue ^3.2.0 ✓
- Pinia requiere Vue ^3.2.0 ✓
- vue-chartjs requiere Chart.js ^4.0.0 ✓

### Lock File

El archivo `package-lock.json` garantiza instalaciones consistentes. Siempre debe:
- ✅ Estar en el repositorio
- ✅ Actualizarse con cada cambio de dependencias
- ✅ Usarse en CI/CD para instalaciones reproducibles

## Dependencias No Instaladas (Consideraciones Futuras)

Estas librerías podrían ser útiles en el futuro:

- **TailwindCSS**: Framework CSS utility-first (actualmente se usa CSS custom)
- **VeeValidate**: Validación de formularios avanzada (actualmente se usa Zod manual)
- **Day.js**: Manipulación de fechas (si se necesita)
- **Vue I18n**: Internacionalización (si se requiere multi-idioma)
- **Pinia Persist**: Persistencia de estado en localStorage
- **@vueuse/core**: Colección de composables útiles

## Resumen de Tamaños

Estimación de tamaños en build de producción:

- **Vendor bundle** (dependencias): ~200-300 KB (gzip)
  - Vue + Router + Pinia: ~80 KB
  - Chart.js + vue-chartjs: ~120 KB
  - Axios: ~15 KB
  - Zod: ~20 KB
  - Heroicons (tree-shaken): ~5 KB

- **App code**: ~50-100 KB (gzip)

- **Total estimado**: ~250-400 KB (gzip)

*Nota: Estos son valores aproximados. El tamaño real puede variar según el uso.*
