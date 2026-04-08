# Modificación a la API de Departamentos y Departamentos Generales

## Contexto del Problema
Anteriormente, las rutas para **Department** y **GeneralDepartment** utilizaban `Route::apiResource()`, lo cual solo proporcionaba endpoints estándar (CRUD). El principal problema de esta implementación era que las solicitudes `GET` a las colecciones devolvían los departamentos de **todas las sucursales**, sin distinguir a qué tienda (`store_id`) pertenecían. Esto estaba provocando un manejo inadecuado de los datos en el Frontend para un sistema multi-sucursal.

## Solución Implementada
Para solucionar esto, se aplicó una refactorización que estandariza las rutas de la misma forma en que están separadas otras entidades (como Empleados). Se eliminó el uso de `apiResource` y en su lugar se crearon múltiples endpoints separados, incluyendo uno específico para filtrar por sucursal. 

### Nuevos Endpoints en Backend

**Para Departamentos Generales (`/api/general-departments`)**:
- `GET /api/general-departments/all`: Obtiene todos los departamentos generales (de todas las tiendas).
- `GET /api/general-departments/store/{storeId}`: **[NUEVO]** Obtiene solo los departamentos generales de una tienda específica.
- `GET /api/general-departments/{id}`: Obtiene el detalle de un departamento general.
- `POST /api/general-departments/create`: Crea un departamento general.
- `PUT /api/general-departments/update/{id}`: Actualiza un departamento general.
- `DELETE /api/general-departments/delete/{id}`: Elimina un departamento general.

**Para Departamentos Específicos (`/api/departments`)**:
- `GET /api/departments/all`: Obtiene todos los departamentos (de todas las tiendas).
- `GET /api/departments/store/{storeId}`: **[NUEVO]** Obtiene solo los departamentos pertenecientes a una tienda específica (mediante su relación con GeneralDepartment).
- `GET /api/departments/{id}`: Obtiene el detalle de un departamento.
- `POST /api/departments/create`: Crea un departamento.
- `PUT /api/departments/update/{id}`: Actualiza un departamento.
- `DELETE /api/departments/delete/{id}`: Elimina un departamento.

---

## Integración en el Frontend

Para asegurar que al trabajar el perfil de una tienda o un registro en una sucursal, solo se muestren los departamentos vinculados a la misma, el Frontend debe llamar explícitamente a las rutas que incluyan el `store_id`.

### Ejemplo con Axios / Vue / React / Vanilla JS

Si el usuario selecciona una Sucursal o está actuando dentro del contexto de una tienda específica:

```javascript
// Variable que guarda el ID de la tienda actual seleccionada
const currentStoreId = 1; 

// 1. OBTENER DEPARTAMENTOS GENERALES POR TIENDA
const fetchGeneralDepartmentsByStore = async (storeId) => {
    try {
        const response = await axios.get(`/api/general-departments/store/${storeId}`, {
            headers: {
                Authorization: `Bearer ${token}` // Asegúrate de enviar tu token de autenticación
            }
        });
        
        // Manejo de la respuesta con los datos
        console.log("Departamentos Generales:", response.data.data);
        return response.data.data;
    } catch (error) {
        console.error("Error al obtener los departamentos generales:", error);
    }
};

// 2. OBTENER DEPARTAMENTOS SUBYACENTES POR TIENDA
const fetchDepartmentsByStore = async (storeId) => {
    try {
        const response = await axios.get(`/api/departments/store/${storeId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        
        // Manejo de la respuesta con los datos
        console.log("Sub-Departamentos:", response.data.data);
        return response.data.data;
    } catch (error) {
        console.error("Error al obtener los departamentos:", error);
    }
};
```

### Consideraciones Frontend:
1. **Actualización de Selectors/Dropdowns**: Si cuentas con un selector (`<select>`) para asignar un departamento, al momento de que el usuario cambie la 'Tienda' debes volver a ejecutar las llamadas (`fetchGeneralDepartmentsByStore` y `fetchDepartmentsByStore`) para resetear tus opciones de departamento actual con los datos apropiados.
2. Formante que esperan las Vistas: Ten en cuenta que la respuesta llega en formato estándar estructurado envuelto en un tag `data` por detrás de la configuración ResourceCollection habitual: `response.data.data`.
