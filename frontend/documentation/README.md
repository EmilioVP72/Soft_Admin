# Documentación Frontend - Soft_Admin

## Índice de Documentación

Esta carpeta contiene la documentación completa del proyecto frontend de Soft_Admin.

### Documentos Disponibles

1. **[SETUP_INSTALLATION.md](./SETUP_INSTALLATION.md)** - Guía de instalación y configuración inicial
2. **[DEPENDENCIES.md](./DEPENDENCIES.md)** - Listado completo de dependencias y sus versiones
3. **[PROJECT_STRUCTURE.md](./PROJECT_STRUCTURE.md)** - Estructura de carpetas y archivos del proyecto
4. **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Arquitectura del proyecto y patrones de diseño utilizados
5. **[COMPONENTS.md](./COMPONENTS.md)** - Documentación de componentes Vue
6. **[STORES_STATE.md](./STORES_STATE.md)** - Gestión de estado con Pinia
7. **[ROUTING.md](./ROUTING.md)** - Configuración de rutas y navegación
8. **[AUTHENTICATION.md](./AUTHENTICATION.md)** - Sistema de autenticación y gestión de sesiones
9. **[API_INTEGRATION.md](./API_INTEGRATION.md)** - Integración con la API backend
10. **[CHARTS_GRAPHICS.md](./CHARTS_GRAPHICS.md)** - Implementación de gráficas con Chart.js
11. **[STYLING.md](./STYLING.md)** - Guía de estilos y CSS

## Resumen Ejecutivo

**Soft_Admin** es una aplicación web de administración para negocios con múltiples sucursales. El frontend está construido con:

- **Vue 3** (Composition API)
- **TypeScript** para type safety
- **Vite** como bundler y dev server
- **Pinia** para gestión de estado
- **Vue Router** para navegación
- **Chart.js** para visualización de datos
- **Axios** para comunicación con la API
- **Zod** para validación de schemas

### Características Principales

- ✅ Sistema de autenticación con JWT
- ✅ Gestión automática de sesiones y renovación de tokens
- ✅ Dashboard con gráficas interactivas de ventas
- ✅ Visualización de datos por departamento y sucursal
- ✅ Diseño responsive y moderno
- ✅ Validación de formularios con Zod
- ✅ Interceptores HTTP para manejo de errores
- ✅ Docker containerization para desarrollo

### Requisitos del Sistema

- Node.js >= 23.7.0
- npm >= 10.x
- Docker y Docker Compose (opcional pero recomendado)

### Inicio Rápido

```bash
# Clonar el repositorio y navegar al frontend
cd frontend

# Instalar dependencias
npm install

# Configurar variables de entorno
cp .env.production .env
# Editar .env con la URL de tu API backend

# Ejecutar en desarrollo
npm run dev

# O con Docker
docker-compose up
```

La aplicación estará disponible en `http://localhost:5173`

## Soporte

Para preguntas o problemas, consulta la documentación específica en los archivos mencionados arriba.
