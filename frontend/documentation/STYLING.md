# Guía de Estilos y CSS

Sistema de estilos del proyecto.

## Estructura de Estilos

```
src/assets/styles/
├── auth/
│   └── login/
│       └── login.css
├── base/
│   ├── navbar.css
│   └── SessionModal.css
└── dashboard/
    ├── dashboard.css
    └── componentes/
        ├── fastActionsDashboard.css
        ├── graphics.css
        └── report.css
```

## Organización

### Por Feature
Cada componente o vista tiene su propio archivo CSS:

```vue
<style src="@/assets/styles/auth/login/login.css"></style>
```

### Scoped vs Global

**Scoped** (solo para BarGraph):
```vue
<style scoped>
.chart-container {
  height: 300px;
}
</style>
```

**Global** (la mayoría):
```vue
<style src="@/assets/styles/base/navbar.css"></style>
```

## Estilos Globales

**Ubicación**: `/src/style.css`

Reset básico y variables globales.

## Convenciones

### Nombres de Clases
- **kebab-case**: `nav-links`, `login-container`
- BEM cuando sea apropiado: `block__element--modifier`

### Estructura
```css
/* Componente principal */
.login-wrapper {
  /* Layout */
}

.login-container {
  /* Container styles */
}

/* Elementos */
.form-group {
  /* Form group */
}

/* Estados */
.input-error {
  /* Error state */
}

/* Responsive */
@media (max-width: 768px) {
  /* Mobile styles */
}
```

## Variables CSS (Futuro)

```css
:root {
  --primary-color: #3b82f6;
  --secondary-color: #64748b;
  --error-color: #ef4444;
  --success-color: #10b981;
  
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
}
```

## Responsive Design

Mobile-first approach:

```css
/* Mobile (por defecto) */
.container {
  width: 100%;
}

/* Tablet */
@media (min-width: 768px) {
  .container {
    width: 750px;
  }
}

/* Desktop */
@media (min-width: 1024px) {
  .container {
    width: 1000px;
  }
}
```

## Framework CSS (No Usado)

Actualmente: **CSS custom**

Futuro posible:
- TailwindCSS
- Bootstrap
- Material Design

## Best Practices

✅ Un archivo CSS por componente/vista
✅ Nombres descriptivos de clases
✅ Responsive design
✅ Evitar !important
✅ Usar imports de assets con `@/`

## Referencias

- [MDN CSS Documentation](https://developer.mozilla.org/en-US/docs/Web/CSS)
