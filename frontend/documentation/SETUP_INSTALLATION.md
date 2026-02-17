# Guía de Instalación y Configuración

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Node.js**: versión 23.7.0 o superior
- **npm**: versión 10.x o superior
- **Docker** (opcional): para desarrollo containerizado
- **Docker Compose** (opcional): para orquestación de contenedores
- **Git**: para control de versiones

## Instalación Paso a Paso

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd Soft_Admin/frontend
```

### 2. Instalar Dependencias

```bash
npm install
```

Este comando instalará todas las dependencias listadas en `package.json`, incluyendo:
- Dependencias de producción (Vue, Pinia, Axios, etc.)
- Dependencias de desarrollo (TypeScript, Vite, etc.)

### 3. Configuración de Variables de Entorno

El proyecto utiliza variables de entorno para la configuración. Crea un archivo `.env` en la raíz del frontend:

```bash
# Puedes copiar desde el archivo de producción como base
cp .env.production .env
```

#### Variables de Entorno Disponibles

Edita el archivo `.env` y configura las siguientes variables:

```env
# URL base de la API backend
VITE_API_BASE_URL=http://localhost:8000/api

# Puerto del servidor de desarrollo (opcional)
# VITE_PORT=5173

# Host del servidor de desarrollo (opcional)
# VITE_HOST=0.0.0.0
```

**Importante**: 
- La variable `VITE_API_BASE_URL` debe apuntar a tu API backend
- Todas las variables de entorno en Vite deben comenzar con el prefijo `VITE_`
- No incluyas el archivo `.env` en el control de versiones (ya está en `.gitignore`)

### 4. Ejecutar en Modo Desarrollo

#### Opción A: Directamente con Node.js

```bash
npm run dev
```

La aplicación estará disponible en:
- **URL local**: `http://localhost:5173`
- **URL de red**: `http://<tu-ip>:5173` (accesible desde otros dispositivos en la red)

#### Opción B: Con Docker (Recomendado)

```bash
# Desde la raíz del proyecto
docker-compose up frontend

# O para todo el stack (backend + frontend + base de datos)
docker-compose up
```

La aplicación estará disponible en:
- **URL**: `http://localhost:5173`

**Ventajas de usar Docker**:
- Entorno consistente entre desarrolladores
- No requiere instalar Node.js localmente
- Hot reload automático con volúmenes montados
- Fácil integración con el backend

### 5. Construir para Producción

```bash
# Compilar TypeScript y construir para producción
npm run build
```

Los archivos optimizados se generarán en la carpeta `dist/`

### 6. Vista Previa de Build de Producción

```bash
npm run preview
```

Esto iniciará un servidor local para previsualizar la build de producción.

## Configuración de Docker

### Dockerfile

El proyecto incluye un `Dockerfile` con las siguientes características:

```dockerfile
FROM node:23.7.0-alpine3.20
# Crea usuario no-root para seguridad
RUN deluser --remove-home node && \
    addgroup -g 1000 devteam && \
    adduser -u 1000 -G devteam -s /bin/sh -D emilio
WORKDIR /app
RUN chown emilio:devteam /app
USER emilio
COPY --chown=emilio:devteam package*.json ./
RUN npm install
COPY --chown=emilio:devteam . .
EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
```

**Características**:
- Basado en Alpine Linux (imagen ligera)
- Usuario no-root para mayor seguridad
- Hot reload habilitado
- Puerto 5173 expuesto

### Docker Compose

El archivo `docker-compose.yml` en la raíz del proyecto configura el frontend con:

```yaml
frontend:
  build: ./frontend
  ports:
    - "5173:5173"
  volumes:
    - ./frontend:/app
    - /app/node_modules
  environment:
    - VITE_API_BASE_URL=http://backend:8000/api
```

## Configuración del Editor

### VS Code (Recomendado)

Extensiones recomendadas:

1. **Volar** - Soporte para Vue 3
2. **TypeScript Vue Plugin (Volar)** - IntelliSense mejorado
3. **ESLint** - Linting
4. **Prettier** - Formateo de código
5. **Docker** - Soporte para Docker

Configuración en `.vscode/settings.json`:

```json
{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "typescript.tsdk": "node_modules/typescript/lib"
}
```

## Solución de Problemas Comunes

### Error: Cannot find module

```bash
# Eliminar node_modules y reinstalar
rm -rf node_modules package-lock.json
npm install
```

### Error: Port 5173 already in use

```bash
# Cambiar el puerto en vite.config.ts o en .env
# O matar el proceso que usa el puerto
lsof -ti:5173 | xargs kill -9
```

### Error de permisos en Docker

```bash
# Reconstruir la imagen sin caché
docker-compose build --no-cache frontend
```

### Hot Reload no funciona en Docker

El proyecto ya está configurado con `usePolling: true` en `vite.config.ts`, lo cual es necesario para que el hot reload funcione dentro de contenedores Docker.

### Error de compilación de TypeScript

```bash
# Verificar versiones de TypeScript
npm list typescript

# Limpiar cache de TypeScript
npm run build -- --force
```

## Verificación de Instalación

Para verificar que todo está correctamente instalado:

```bash
# Ver versión de Node
node --version  # Debe ser >= 23.7.0

# Ver versión de npm
npm --version   # Debe ser >= 10.x

# Verificar dependencias
npm list --depth=0

# Ejecutar en desarrollo
npm run dev
```

Si ves el mensaje:
```
VITE v7.2.4  ready in XXX ms

➜  Local:   http://localhost:5173/
➜  Network: http://192.168.x.x:5173/
```

¡La instalación fue exitosa!

## Próximos Pasos

Una vez instalado correctamente:

1. Revisa [PROJECT_STRUCTURE.md](./PROJECT_STRUCTURE.md) para entender la estructura del proyecto
2. Lee [ARCHITECTURE.md](./ARCHITECTURE.md) para comprender la arquitectura
3. Consulta [AUTHENTICATION.md](./AUTHENTICATION.md) para configurar la autenticación
4. Explora [COMPONENTS.md](./COMPONENTS.md) para conocer los componentes disponibles
